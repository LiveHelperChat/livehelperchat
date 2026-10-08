#!/usr/bin/env bash
#
# Live Helper Chat + self hosted Voice & Video (LiveKit, TURN, recording) deploy script.
# Supports Ubuntu/Debian and AlmaLinux/Rocky/RHEL 8-9. Run as root.
#
#   curl -fsSL https://raw.githubusercontent.com/perfectwebtech/livehelperchat/claude/compassionate-bardeen-n40rwu/lhc_web/doc/voice_video/deploy/deploy.sh -o deploy.sh
#   LHC_DOMAIN=livehelper.example.com RTC_DOMAIN=rtc.example.com LE_EMAIL=admin@example.com bash deploy.sh
#
# Safe to re-run. Existing nginx sites are not modified. Generated passwords and keys are stored in
# /root/lhc-voice-deploy.env and reused on the next run.
#
# Options (environment variables)
#   LHC_DOMAIN     chat domain (required)
#   RTC_DOMAIN     media server domain, used for wss signalling and TURN (required)
#   LE_EMAIL       email for Let's Encrypt (required unless SKIP_TLS=1)
#   BRANCH         git branch (default claude/compassionate-bardeen-n40rwu)
#   INSTALL_DIR    default /var/www/livehelperchat
#   PUBLIC_IP      public IP of this server (default: resolved from RTC_DOMAIN)
#   SKIP_TLS=1     do not request certificates (calls need HTTPS in browsers, testing only)
#   FORCE=1        continue even if LHC_DOMAIN/RTC_DOMAIN is already used by another nginx site

set -Eeuo pipefail

REPO="${REPO:-https://github.com/perfectwebtech/livehelperchat.git}"
BRANCH="${BRANCH:-claude/compassionate-bardeen-n40rwu}"
INSTALL_DIR="${INSTALL_DIR:-/var/www/livehelperchat}"
LK_DIR="${LK_DIR:-/opt/lhc-livekit}"
RECORDINGS_DIR="${RECORDINGS_DIR:-/var/lib/lhc-recordings}"
STATE_FILE="/root/lhc-voice-deploy.env"
LOG_FILE="/root/lhc-voice-deploy-$(date +%Y%m%d-%H%M%S).log"
LIVEKIT_IMAGE="livekit/livekit-server:v1.9"
EGRESS_IMAGE="livekit/egress:v1.10"

exec > >(tee -a "$LOG_FILE") 2>&1

step() { echo; echo "==> $*"; }
info() { echo "    $*"; }
warn() { echo "    WARNING: $*"; }
die()  { echo; echo "ERROR: $*"; echo "Log: $LOG_FILE"; exit 1; }
trap 'die "failed at line $LINENO: $BASH_COMMAND"' ERR

rand() { tr -dc 'A-Za-z0-9' < /dev/urandom | head -c "$1" || true; }
as_web() { runuser -u "$WEB_USER" -- "$@"; }

# ---------------------------------------------------------------------------
step "Preflight"

[ "$(id -u)" = "0" ] || die "run as root"
[ -n "${LHC_DOMAIN:-}" ] || die "LHC_DOMAIN is required"
[ -n "${RTC_DOMAIN:-}" ] || die "RTC_DOMAIN is required"
[ "${SKIP_TLS:-0}" = "1" ] || [ -n "${LE_EMAIL:-}" ] || die "LE_EMAIL is required for Let's Encrypt (or SKIP_TLS=1)"
if [ "${SKIP_TLS:-0}" = "1" ]; then SCHEME=http; WS_SCHEME=ws; else SCHEME=https; WS_SCHEME=wss; fi

. /etc/os-release
case "${ID} ${ID_LIKE:-}" in
    *ubuntu*|*debian*) OS_FAMILY=debian ;;
    *rhel*|*centos*|*fedora*|*almalinux*|*rocky*) OS_FAMILY=rhel ;;
    *) die "unsupported OS: ${PRETTY_NAME}" ;;
esac
info "OS: ${PRETTY_NAME} (${OS_FAMILY})"

if [ -z "${PUBLIC_IP:-}" ]; then
    PUBLIC_IP="$(getent ahostsv4 "$RTC_DOMAIN" | awk 'NR==1{print $1}')"
fi
[ -n "$PUBLIC_IP" ] || die "could not resolve $RTC_DOMAIN, set PUBLIC_IP"
info "Public IP (WebRTC node_ip): $PUBLIC_IP"

LOCAL_IPS="$(hostname -I 2>/dev/null || true)"
if ! echo " $LOCAL_IPS " | grep -q " $PUBLIC_IP "; then
    warn "$PUBLIC_IP is not configured on this server ($LOCAL_IPS). Fine if the server is behind 1:1 NAT, otherwise check DNS."
fi
for d in "$LHC_DOMAIN" "$RTC_DOMAIN"; do
    ip="$(getent ahostsv4 "$d" | awk 'NR==1{print $1}')"
    [ "$ip" = "$PUBLIC_IP" ] || warn "$d resolves to '${ip:-nothing}', expected $PUBLIC_IP. Certificates will fail if DNS is wrong."
done

# Persistent secrets
if [ -f "$STATE_FILE" ]; then
    info "Reusing generated credentials from $STATE_FILE"
    . "$STATE_FILE"
fi
# Unique names, shared servers often already have "livehelperchat"/"lhc" for other sites
DB_NAME="${DB_NAME:-lhc_voicevideo}"
DB_USER="${DB_USER:-lhc_voicevideo}"
DB_PASS="${DB_PASS:-$(rand 24)}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-$(rand 16)}"
OPERATOR_USER="${OPERATOR_USER:-operator2}"
OPERATOR_PASS="${OPERATOR_PASS:-$(rand 16)}"
LK_API_KEY="${LK_API_KEY:-API$(rand 12)}"
LK_API_SECRET="${LK_API_SECRET:-$(rand 48)}"
umask 077
cat > "$STATE_FILE" <<EOF
DB_NAME='$DB_NAME'
DB_USER='$DB_USER'
DB_PASS='$DB_PASS'
ADMIN_USER='$ADMIN_USER'
ADMIN_PASS='$ADMIN_PASS'
OPERATOR_USER='$OPERATOR_USER'
OPERATOR_PASS='$OPERATOR_PASS'
LK_API_KEY='$LK_API_KEY'
LK_API_SECRET='$LK_API_SECRET'
EOF
umask 022

# ---------------------------------------------------------------------------
step "Existing web server"

if command -v nginx >/dev/null 2>&1; then
    info "nginx found: $(nginx -v 2>&1)"
    info "Existing server names:"
    nginx -T 2>/dev/null | grep -E '^\s*server_name' | sed 's/^\s*/      /' | sort -u || true
    for d in "$LHC_DOMAIN" "$RTC_DOMAIN"; do
        # Config files (other than ours) which already serve this domain
        OTHER="$(grep -rlE "server_name[^;]*[[:space:]]${d//./\\.}([[:space:];]|$)" /etc/nginx 2>/dev/null | xargs -r grep -L "lhc-voice-deploy" 2>/dev/null || true)"
        if [ -n "$OTHER" ]; then
            echo "    $d is already configured in: $OTHER"
            [ "${FORCE:-0}" = "1" ] || die "$d is already served by another nginx site. Remove it or run with FORCE=1."
        fi
    done
else
    if ss -ltn '( sport = :80 or sport = :443 )' | grep -q LISTEN; then
        die "ports 80/443 are used by another web server (not nginx). This script configures nginx only."
    fi
fi

for port in 7880 7881; do
    if ss -ltn "( sport = :$port )" | grep -q LISTEN && ! docker ps --format '{{.Names}}' 2>/dev/null | grep -q '^lhc-livekit'; then
        die "port $port is already in use"
    fi
done
REDIS_PORT=6379
if ss -ltn '( sport = :6379 )' | grep -q LISTEN && ! docker ps --format '{{.Names}}' 2>/dev/null | grep -q '^lhc-livekit-redis'; then
    REDIS_PORT=6380
    info "Port 6379 is used, LiveKit redis will use $REDIS_PORT"
fi

# ---------------------------------------------------------------------------
step "Packages"

if [ "$OS_FAMILY" = "debian" ]; then
    export DEBIAN_FRONTEND=noninteractive
    apt-get update -q
    PKGS="nginx git curl ca-certificates gnupg acl unzip php-fpm php-cli php-mysql php-gd php-curl php-mbstring php-xml php-zip php-bcmath php-intl certbot python3-certbot-nginx"
    command -v mysql >/dev/null 2>&1 || PKGS="$PKGS mariadb-server"
    apt-get install -y -q $PKGS
    PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
    PHP_FPM_SERVICE="php${PHP_VER}-fpm"
    PHP_FPM_SOCK="/run/php/php${PHP_VER}-fpm.sock"
    WEB_USER="www-data"
    if [ -d /etc/nginx/sites-available ]; then NGINX_SITE_DIR=/etc/nginx/sites-available; NGINX_LINK_DIR=/etc/nginx/sites-enabled; else NGINX_SITE_DIR=/etc/nginx/conf.d; NGINX_LINK_DIR=""; fi
    if ! command -v docker >/dev/null 2>&1; then
        install -m 0755 -d /etc/apt/keyrings
        curl -fsSL "https://download.docker.com/linux/${ID}/gpg" -o /etc/apt/keyrings/docker.asc
        echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/${ID} ${VERSION_CODENAME} stable" > /etc/apt/sources.list.d/docker.list
        apt-get update -q
        apt-get install -y -q docker-ce docker-ce-cli containerd.io docker-compose-plugin
    fi
else
    dnf -y install dnf-plugins-core epel-release || dnf -y install dnf-plugins-core
    if ! command -v php >/dev/null 2>&1; then
        dnf -y module reset php || true
        dnf -y module enable php:8.2 || true
    fi
    PKGS="nginx git curl acl unzip policycoreutils-python-utils php-fpm php-cli php-mysqlnd php-gd php-mbstring php-xml php-bcmath php-intl php-pdo php-process certbot python3-certbot-nginx"
    command -v mysql >/dev/null 2>&1 || PKGS="$PKGS mariadb-server"
    dnf -y install $PKGS
    PHP_FPM_SERVICE="php-fpm"
    PHP_FPM_SOCK="/run/php-fpm/www.sock"
    WEB_USER="$(awk -F'= *' '/^user *=/{print $2}' /etc/php-fpm.d/www.conf | tr -d ' ')"
    WEB_USER="${WEB_USER:-apache}"
    NGINX_SITE_DIR=/etc/nginx/conf.d; NGINX_LINK_DIR=""
    if ! command -v docker >/dev/null 2>&1; then
        dnf config-manager --add-repo https://download.docker.com/linux/rhel/docker-ce.repo
        dnf -y install --allowerasing docker-ce docker-ce-cli containerd.io docker-compose-plugin
    fi
fi

php -r 'exit(version_compare(PHP_VERSION, "7.4.0", ">=") ? 0 : 1);' || die "PHP 7.4+ required, found $(php -r 'echo PHP_VERSION;')"
WEB_GROUP="$(id -gn "$WEB_USER")"
WEB_GID="$(id -g "$WEB_USER")"
info "PHP $(php -r 'echo PHP_VERSION;'), fpm socket $PHP_FPM_SOCK, web user $WEB_USER:$WEB_GROUP ($WEB_GID)"

systemctl enable --now docker 2>/dev/null || docker info >/dev/null 2>&1 || die "docker is not running"
systemctl enable --now "$PHP_FPM_SERVICE"
systemctl enable --now nginx
if systemctl list-unit-files | grep -q '^mariadb.service'; then systemctl enable --now mariadb; fi

# ---------------------------------------------------------------------------
step "Database"

MYSQL="mysql"
$MYSQL -e "SELECT 1" >/dev/null 2>&1 || die "can not connect to MySQL/MariaDB as root. Create /root/.my.cnf with root credentials and re-run."
# Never touch a database or user created by somebody else
DB_MARKER="/root/.lhc-voice-deploy-db-$DB_NAME"
if [ ! -f "$DB_MARKER" ]; then
    [ -z "$($MYSQL -N -e "SHOW DATABASES LIKE '$DB_NAME'")" ] || die "database $DB_NAME already exists and was not created by this script. Re-run with DB_NAME=<new name>."
    [ "$($MYSQL -N -e "SELECT COUNT(*) FROM mysql.user WHERE User='$DB_USER'")" = "0" ] || die "database user $DB_USER already exists and was not created by this script. Re-run with DB_USER=<new name>."
fi
$MYSQL -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
$MYSQL -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
$MYSQL -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"
touch "$DB_MARKER"
info "Database $DB_NAME ready"

# ---------------------------------------------------------------------------
step "Live Helper Chat code ($BRANCH)"

if [ -d "$INSTALL_DIR/.git" ]; then
    git -C "$INSTALL_DIR" fetch --depth 1 origin "$BRANCH"
    git -C "$INSTALL_DIR" checkout -q -B "$BRANCH" FETCH_HEAD
else
    [ ! -e "$INSTALL_DIR" ] || [ -z "$(ls -A "$INSTALL_DIR")" ] || die "$INSTALL_DIR exists and is not a git checkout"
    git clone -q --depth 1 --branch "$BRANCH" "$REPO" "$INSTALL_DIR"
fi
WEBROOT="$INSTALL_DIR/lhc_web"
info "Commit $(git -C "$INSTALL_DIR" rev-parse --short HEAD)"

chown -R root:root "$INSTALL_DIR"
for d in cache settings var; do
    chown -R "$WEB_USER:$WEB_GROUP" "$WEBROOT/$d"
    chmod -R u+rwX "$WEBROOT/$d"
done
# Static js/css compilation directories
chown -R "$WEB_USER:$WEB_GROUP" "$WEBROOT/design/defaulttheme/js/js_static" "$WEBROOT/design/defaulttheme/css/css_static" 2>/dev/null || true

if [ "$OS_FAMILY" = "rhel" ] && command -v getenforce >/dev/null 2>&1 && [ "$(getenforce)" != "Disabled" ]; then
    info "Configuring SELinux contexts"
    semanage fcontext -a -t httpd_sys_content_t "$WEBROOT(/.*)?" 2>/dev/null || semanage fcontext -m -t httpd_sys_content_t "$WEBROOT(/.*)?"
    for d in cache settings var design/defaulttheme/js/js_static design/defaulttheme/css/css_static; do
        semanage fcontext -a -t httpd_sys_rw_content_t "$WEBROOT/$d(/.*)?" 2>/dev/null || semanage fcontext -m -t httpd_sys_rw_content_t "$WEBROOT/$d(/.*)?"
    done
    semanage fcontext -a -t httpd_sys_rw_content_t "$RECORDINGS_DIR(/.*)?" 2>/dev/null || semanage fcontext -m -t httpd_sys_rw_content_t "$RECORDINGS_DIR(/.*)?"
    restorecon -R "$WEBROOT"
    # PHP calls LiveKit API, nginx proxies to LiveKit
    setsebool -P httpd_can_network_connect 1
fi

# ---------------------------------------------------------------------------
step "Live Helper Chat install"

if [ -z "$($MYSQL -N -e "SHOW TABLES LIKE 'lh_users'" "$DB_NAME")" ]; then
    cp "$WEBROOT/settings/settings.ini.default.php" "$WEBROOT/settings/settings.ini.php"
    chown "$WEB_USER:$WEB_GROUP" "$WEBROOT/settings/settings.ini.php"
    INI="$(mktemp)"
    cat > "$INI" <<EOF
[db]
host = localhost
user = $DB_USER
password = $DB_PASS
database = $DB_NAME
port = 3306

[admin]
AdminUsername = $ADMIN_USER
AdminPassword = $ADMIN_PASS
AdminEmail = ${LE_EMAIL:-admin@$LHC_DOMAIN}
AdminName = Admin
AdminSurname = User
Domain = $LHC_DOMAIN
DefaultDepartament = Support
ForceVirtualHost = 0
Extensions =
ApacheUserGroupName = $WEB_GROUP
ApacheUserName = $WEB_USER
TimeZone = UTC
WebhooksEnabled = 0
WebhooksWorker = http
DefaultConfigs = []
EOF
    # install-cli.php exits with 1 even on success, check the result in the database instead
    (cd "$WEBROOT" && php cli/install-cli.php "$INI" 2>&1 | grep -v -i deprecat) || true
    [ -n "$($MYSQL -N -e "SHOW TABLES LIKE 'lh_users'" "$DB_NAME")" ] || die "Live Helper Chat install failed"
    rm -f "$INI"
    chown -R "$WEB_USER:$WEB_GROUP" "$WEBROOT/settings" "$WEBROOT/cache" "$WEBROOT/var"
    info "Installed, admin user: $ADMIN_USER"
else
    info "Already installed, running database update"
    [ -f "$WEBROOT/settings/settings.ini.php" ] || die "database $DB_NAME has tables but $WEBROOT/settings/settings.ini.php is missing. Restore it from backup."
fi

(cd "$WEBROOT" && as_web php cron.php -s site_admin -c cron/util/update_database -p local 2>&1 | grep -v -i deprecat | tail -2) || warn "database update reported a problem"

# ---------------------------------------------------------------------------
step "Recordings directory"

mkdir -p "$RECORDINGS_DIR"
chown 1001:"$WEB_GID" "$RECORDINGS_DIR"
chmod 2775 "$RECORDINGS_DIR"
[ "$OS_FAMILY" = "rhel" ] && command -v restorecon >/dev/null 2>&1 && restorecon -R "$RECORDINGS_DIR" || true
info "$RECORDINGS_DIR (owner 1001:$WEB_GROUP, setgid)"

# ---------------------------------------------------------------------------
step "LiveKit, Redis, Egress ($LK_DIR)"

mkdir -p "$LK_DIR"
cat > "$LK_DIR/livekit.yaml" <<EOF
# lhc-voice-deploy generated
port: 7880
rtc:
  tcp_port: 7881
  port_range_start: 50000
  port_range_end: 60000
  # Fully self hosted: no public STUN service is contacted
  use_external_ip: false
  node_ip: $PUBLIC_IP
  stun_servers:
    - $RTC_DOMAIN:3478
redis:
  address: 127.0.0.1:$REDIS_PORT
keys:
  $LK_API_KEY: $LK_API_SECRET
turn:
  enabled: true
  domain: $RTC_DOMAIN
  udp_port: 3478
  relay_range_start: 30000
  relay_range_end: 40000
webhook:
  api_key: $LK_API_KEY
  urls:
    - $SCHEME://$LHC_DOMAIN/index.php/voicevideo/webhook
room:
  max_participants: 6
  empty_timeout: 300
logging:
  level: info
EOF

cat > "$LK_DIR/egress.yaml" <<EOF
# lhc-voice-deploy generated
api_key: $LK_API_KEY
api_secret: $LK_API_SECRET
ws_url: ws://127.0.0.1:7880
redis:
  address: 127.0.0.1:$REDIS_PORT
health_port: 7990
log_level: info
EOF
chmod 600 "$LK_DIR/livekit.yaml" "$LK_DIR/egress.yaml"
chown 1001 "$LK_DIR/egress.yaml"

cat > "$LK_DIR/docker-compose.yml" <<EOF
# lhc-voice-deploy generated
name: lhc-livekit
services:
  redis:
    container_name: lhc-livekit-redis
    image: redis:7-alpine
    restart: unless-stopped
    network_mode: host
    command: redis-server --bind 127.0.0.1 --port $REDIS_PORT --protected-mode yes
  livekit:
    container_name: lhc-livekit
    image: $LIVEKIT_IMAGE
    restart: unless-stopped
    network_mode: host
    command: --config /etc/livekit.yaml
    volumes:
      - ./livekit.yaml:/etc/livekit.yaml:ro
    depends_on: [redis]
  egress:
    container_name: lhc-livekit-egress
    image: $EGRESS_IMAGE
    restart: unless-stopped
    network_mode: host
    user: "1001:$WEB_GID"
    entrypoint: ["/bin/bash", "-c", "umask 0002 && exec /entrypoint.sh"]
    cap_add: [SYS_ADMIN]
    environment:
      - EGRESS_CONFIG_FILE=/etc/egress.yaml
    volumes:
      - ./egress.yaml:/etc/egress.yaml:ro
      - $RECORDINGS_DIR:/out
    depends_on: [livekit]
EOF

docker compose -f "$LK_DIR/docker-compose.yml" pull -q
docker compose -f "$LK_DIR/docker-compose.yml" up -d --remove-orphans
info "Containers: $(docker ps --filter name=lhc-livekit --format '{{.Names}}' | tr '\n' ' ')"

# ---------------------------------------------------------------------------
step "nginx sites"

site_file() {
    if [ -n "$NGINX_LINK_DIR" ]; then echo "$NGINX_SITE_DIR/$1"; else echo "$NGINX_SITE_DIR/$1.conf"; fi
}
enable_site() {
    if [ -n "$NGINX_LINK_DIR" ]; then ln -sf "$NGINX_SITE_DIR/$1" "$NGINX_LINK_DIR/$1"; fi
}

LHC_SITE="$(site_file "lhc-$LHC_DOMAIN")"
RTC_SITE="$(site_file "lhc-$RTC_DOMAIN")"

# Do not overwrite sites already modified by certbot (they contain ssl settings)
if [ ! -f "$LHC_SITE" ] || ! grep -q "managed by Certbot" "$LHC_SITE"; then
cat > "$LHC_SITE" <<EOF
# lhc-voice-deploy generated
server {
    listen 80;
    listen [::]:80;
    server_name $LHC_DOMAIN;
    root $WEBROOT;
    index index.php;
    client_max_body_size 50m;

    # Private directories
    location ~ ^/(settings|cache|cli|doc|pos|lib|modules|translations|ezcomponents|extension/[^/]+/(settings|classes|modules))/ { deny all; }
    location ~ ^/var/(storage|storagedocshare|storageform|tmpfiles|storageadmintheme)/ { deny all; }
    location ~ /\.(git|ht) { deny all; }
    location = /cron.php { deny all; }

    location / {
        try_files \$uri \$uri/ /index.php\$is_args\$args;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|svg|ico|woff2?|ttf|eot|otf|mp3|ogg|wav|wasm)$ {
        add_header Access-Control-Allow-Origin "*";
        expires 7d;
        try_files \$uri =404;
    }

    location ~ [^/]\.php(/|$) {
        fastcgi_split_path_info ^(.+?\.php)(/.*)$;
        if (!-f \$document_root\$fastcgi_script_name) { return 404; }
        fastcgi_pass unix:$PHP_FPM_SOCK;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        fastcgi_param HTTP_AUTHORIZATION \$http_authorization;
        fastcgi_read_timeout 120;
    }
}
EOF
fi

if [ ! -f "$RTC_SITE" ] || ! grep -q "managed by Certbot" "$RTC_SITE"; then
cat > "$RTC_SITE" <<EOF
# lhc-voice-deploy generated
map \$http_upgrade \$lhc_lk_connection_upgrade {
    default upgrade;
    ''      close;
}
server {
    listen 80;
    listen [::]:80;
    server_name $RTC_DOMAIN;

    location / {
        proxy_pass http://127.0.0.1:7880;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection \$lhc_lk_connection_upgrade;
        proxy_set_header Host \$host;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_read_timeout 86400;
        proxy_send_timeout 86400;
    }
}
EOF
fi

enable_site "lhc-$LHC_DOMAIN"
enable_site "lhc-$RTC_DOMAIN"
nginx -t || die "nginx configuration test failed, nothing was reloaded"
systemctl reload nginx
info "$LHC_SITE"
info "$RTC_SITE"

# ---------------------------------------------------------------------------
step "Firewall"

if command -v ufw >/dev/null 2>&1 && ufw status | grep -q "Status: active"; then
    ufw allow 80/tcp; ufw allow 443/tcp; ufw allow 7881/tcp
    ufw allow 3478/udp; ufw allow 50000:60000/udp; ufw allow 30000:40000/udp
    info "ufw rules added"
elif command -v firewall-cmd >/dev/null 2>&1 && firewall-cmd --state >/dev/null 2>&1; then
    firewall-cmd -q --permanent --add-service=http --add-service=https
    firewall-cmd -q --permanent --add-port=7881/tcp --add-port=3478/udp --add-port=50000-60000/udp --add-port=30000-40000/udp
    firewall-cmd -q --reload
    info "firewalld rules added"
else
    info "No active host firewall found"
fi
info "If your hosting provider has a network firewall/security group, open there too:"
info "  80/tcp 443/tcp 7881/tcp 3478/udp 50000-60000/udp 30000-40000/udp"

# ---------------------------------------------------------------------------
step "TLS certificates"

if [ "${SKIP_TLS:-0}" = "1" ]; then
    warn "SKIP_TLS=1, browsers will block camera/microphone on http"
else
    certbot --nginx --non-interactive --agree-tos --redirect -m "$LE_EMAIL" -d "$LHC_DOMAIN" -d "$RTC_DOMAIN" \
        || die "certbot failed. Check that DNS of $LHC_DOMAIN and $RTC_DOMAIN points here and port 80 is open."
fi

# ---------------------------------------------------------------------------
step "Voice & Video configuration, test users"

CFG_PHP="$(mktemp --suffix=.php)"
cat > "$CFG_PHP" <<'PHP'
<?php
chdir(getenv('LHC_WEBROOT'));
require_once "lib/core/lhcore/password.php";
require_once "ezcomponents/Base/src/base.php";
ezcBase::addClassRepository('./', './lib/autoloads');
spl_autoload_register(array('ezcBase', 'autoload'), true, false);
$_SERVER['REQUEST_URI'] = '/';
erLhcoreClassSystem::init();
ezcBaseInit::setCallback('ezcInitDatabaseInstance', 'erLhcoreClassLazyDatabaseConfiguration');

$config = erLhcoreClassModelChatConfig::fetch('vvsh_configuration');
$data = (array)$config->data;
$data = array_merge($data, array(
    'provider' => 'livekit',
    'voice' => true,
    'video' => true,
    'screenshare' => true,
    'log_calls' => true,
    'livekit_url' => getenv('LK_URL'),
    'livekit_api_url' => 'http://127.0.0.1:7880',
    'livekit_api_key' => getenv('LK_API_KEY'),
    'livekit_api_secret' => getenv('LK_API_SECRET'),
    'ring_timeout' => isset($data['ring_timeout']) ? $data['ring_timeout'] : 60,
    'token_ttl' => isset($data['token_ttl']) ? $data['token_ttl'] : 0,
    'recording_mode' => isset($data['recording_mode']) ? $data['recording_mode'] : 'manual',
    'recording_audio_only' => isset($data['recording_audio_only']) ? $data['recording_audio_only'] : false,
    'recording_egress_path' => '/out',
    'recording_storage_dir' => getenv('RECORDINGS_DIR'),
    'recording_retention_days' => isset($data['recording_retention_days']) ? $data['recording_retention_days'] : 90,
));
unset($data['agora_app_id'], $data['agora_app_token']);
$config->identifier = 'vvsh_configuration';
$config->type = 0;
$config->hidden = 1;
$config->explain = '';
$config->value = serialize($data);
$config->saveThis();

// Second operator for transfer tests
$username = getenv('OPERATOR_USER');
if (!(erLhcoreClassModelUser::findOne(array('filter' => array('username' => $username))) instanceof erLhcoreClassModelUser)) {
    $user = new erLhcoreClassModelUser();
    $user->username = $username;
    $user->email = $username . '@' . getenv('LHC_DOMAIN');
    $user->name = 'Second';
    $user->surname = 'Operator';
    $user->setPassword(getenv('OPERATOR_PASS'));
    $user->all_departments = 1;
    $user->disabled = 0;
    $user->saveThis();

    // Administrators group, so all call features can be tested
    $groupUser = new erLhcoreClassModelGroupUser();
    $groupUser->group_id = 1;
    $groupUser->user_id = $user->id;
    $groupUser->saveThis();

    // All departments
    $db = ezcDbInstance::get();
    $stmt = $db->prepare('INSERT INTO lh_userdep (user_id,dep_id,last_activity,hide_online,last_accepted,active_chats,type,dep_group_id,exclude_autoasign,always_on,ro,pending_chats,inactive_chats,max_chats,lastd_activity,exc_indv_autoasign,assign_priority,chat_max_priority,chat_min_priority) VALUES (:user_id,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)');
    $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
    $stmt->execute();
    echo "Created operator " . $username . "\n";
}

$cache = erConfigClassLhCacheConfig::getInstance();
$cache->expireCache();
echo "Voice & Video configured\n";
PHP
chmod 644 "$CFG_PHP"
(cd "$WEBROOT" && as_web env LHC_WEBROOT="$WEBROOT" LK_URL="$WS_SCHEME://$RTC_DOMAIN" LK_API_KEY="$LK_API_KEY" LK_API_SECRET="$LK_API_SECRET" \
    RECORDINGS_DIR="$RECORDINGS_DIR" OPERATOR_USER="$OPERATOR_USER" OPERATOR_PASS="$OPERATOR_PASS" LHC_DOMAIN="$LHC_DOMAIN" \
    php "$CFG_PHP" 2>&1 | grep -v -i deprecat) || die "configuration failed"
rm -f "$CFG_PHP"

# ---------------------------------------------------------------------------
step "Cron jobs"

cat > /etc/cron.d/livehelperchat <<EOF
# lhc-voice-deploy generated
* * * * * $WEB_USER cd $WEBROOT && php cron.php -s site_admin -c cron/workflow > /dev/null 2>&1
30 3 * * * $WEB_USER cd $WEBROOT && php cron.php -s site_admin -c cron/voicevideo_recordings > /dev/null 2>&1
EOF
chmod 644 /etc/cron.d/livehelperchat
info "/etc/cron.d/livehelperchat"

# ---------------------------------------------------------------------------
step "Health checks"

sleep 3
check() {
    local name="$1" expected="$2" got="$3"
    if [ "$got" = "$expected" ]; then echo "    OK    $name"; else echo "    FAIL  $name (expected $expected, got $got)"; fi
}
check "chat login page" "200" "$(curl -s -o /dev/null -w '%{http_code}' "$SCHEME://$LHC_DOMAIN/index.php/site_admin/user/login")"
check "LiveKit via nginx" "200" "$(curl -s -o /dev/null -w '%{http_code}' "$SCHEME://$RTC_DOMAIN/")"
check "webhook rejects unsigned requests" "401" "$(curl -s -o /dev/null -w '%{http_code}' -X POST "$SCHEME://$LHC_DOMAIN/index.php/voicevideo/webhook" -d '{}')"
check "private settings directory" "403" "$(curl -s -o /dev/null -w '%{http_code}' "$SCHEME://$LHC_DOMAIN/settings/settings.ini.php")"
for c in lhc-livekit lhc-livekit-redis lhc-livekit-egress; do
    check "container $c" "running" "$(docker inspect -f '{{.State.Status}}' "$c" 2>/dev/null || echo missing)"
done

cat <<EOF

=====================================================================
 Done. Credentials are stored in $STATE_FILE (root only).

 Admin panel  : $SCHEME://$LHC_DOMAIN/index.php/site_admin/
 Admin        : $ADMIN_USER / $ADMIN_PASS
 Operator 2   : $OPERATOR_USER / $OPERATOR_PASS   (transfer tests)
 Media server : $WS_SCHEME://$RTC_DOMAIN  (TURN $RTC_DOMAIN:3478/udp)
 Recordings   : $RECORDINGS_DIR
 Log          : $LOG_FILE

 Change the admin password after first login.
 Embed code for your website: Settings -> Embed code.
=====================================================================
EOF
