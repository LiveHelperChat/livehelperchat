## Install

```
yum install nodejs npm
npm install socket.io
npm install forever
```

You may need to symlink forever to /user/bin/forever

```shell script
adduser nodejs
vim /usr/lib/systemd/system/nodejscobrowse.service
```

Service file example

```
[Unit]
Description=Live helper Chat NodeJs Co-Browsing server

[Service]
User=nodejs
ExecStart=/usr/bin/forever /var/www/client/lhc_web/doc/node_servers/cobrowsing/server.js
LimitNOFILE=100000

[Install]
WantedBy=multi-user.target
```

You may want to edit settings.js file if you want to run it on just for testing purposes.

## Live Helper chat changes

In Live Helper Chat if you are using `/wsnodejs/` in your nginx config.

Go to `Settings -> Live help confgiuration -> Chat configuration -> (Screen sharing)` and

* Check NodeJs support enabled
* In socket.io path enter `/wsnodejs/socket.io`

Location of `Location of SocketIO JS library` should be

https://cdn.jsdelivr.net/npm/socket.io-client@2/dist/socket.io.js
