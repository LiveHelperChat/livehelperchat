yum install nodejs npm
npm install socket.io
npm install forever

You may need to symlink forever to /user/bin/forever
--------------------------

adduser nodejs

--------------------------

vim /usr/lib/systemd/system/nodejscobrowse.service

[Unit]
Description=Live helper Chat NodeJs Co-Browsing server

[Service]
User=nodejs
ExecStart=/usr/bin/forever /var/www/client/lhc_web/doc/node_servers/cobrowsing/server.js
LimitNOFILE=100000

[Install]
WantedBy=multi-user.target

--------------------------

You may want to edit settings.js file if you want to run it on just for testing purposes.