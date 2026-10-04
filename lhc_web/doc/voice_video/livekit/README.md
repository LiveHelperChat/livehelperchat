# Self hosted Voice & Video & ScreenShare with LiveKit

[LiveKit](https://livekit.io) is an open source (Apache 2.0) WebRTC SFU media server.
With LiveKit provider audio/video/screen share media never leaves your own infrastructure.

## Requirements

* Linux server with public IP, Docker and Docker Compose
* DNS record for `livekit.example.com` (and optional `turn.example.com`)
* Open firewall ports

| Port | Protocol | Purpose |
|------|----------|---------|
| 443 | TCP | `wss://` signalling (Caddy TLS) |
| 80 | TCP | Let's Encrypt certificate issuing |
| 7881 | TCP | WebRTC over TCP fallback |
| 50000-60000 | UDP | WebRTC media |
| 3478 | UDP | TURN |
| 30000-40000 | UDP | TURN relay |
| 6379 | TCP | Redis, bind to 127.0.0.1 only, never expose |
| 5349 | TCP | TURN over TLS (optional, needs certificate, see `livekit.yaml`) |

## Install

1. Generate API key and secret

   ```
   docker run --rm livekit/livekit-server:v1.9 generate-keys
   ```

2. Replace `REPLACE_API_KEY`, `REPLACE_API_SECRET`, `203.0.113.10` (server public IP), `turn.example.com` and the webhook URL (`chat.example.com`) in `livekit.yaml` and `livekit.example.com` in `Caddyfile`.
   The webhook URL is also shown on the Live Helper Chat configuration page.
3. Start

   ```
   docker compose up -d
   ```

4. In Live Helper Chat open `System configuration -> Voice & Video & ScreenShare -> Configuration`
   * Media provider - `LiveKit`
   * LiveKit server URL - `wss://livekit.example.com`
   * API key / API secret - generated values
   * Check `Calls enabled`, optionally `Video enabled`, `ScreenShare enabled`

5. Run database update if you are upgrading an existing installation

   ```
   php cron.php -s site_admin -c cron/util/update_database -p local
   ```

## No third party services

Everything runs on your servers:

* Media server (LiveKit SFU), TURN/STUN (built in LiveKit TURN) and signalling are self hosted.
* The browser SDK is bundled with Live Helper Chat and served from your domain.
* `rtc.stun_servers` and `rtc.node_ip` in `livekit.yaml` must be set. Otherwise LiveKit uses Google STUN to discover its IP and passes Google/Twilio STUN servers to browsers.

Check what browsers receive: open the call window, `chrome://webrtc-internals` -> `iceServers` must contain only your own hosts.

## Call recording

1. Create the recordings directory on the host: `mkdir -p /var/lib/lhc-recordings && chown 1001:1001 /var/lib/lhc-recordings` (egress runs as uid 1001). Live Helper Chat web server user needs read (and delete for retention) access.
2. Put API key/secret into `egress.yaml`. `redis` in `livekit.yaml` must be enabled.
3. `docker compose up -d` starts redis and egress as well.
4. Live Helper Chat -> Voice & Video configuration -> Call recording
   * Recording - `Manual` (operator presses record button) or `Automatic` (every answered call)
   * Egress output directory - `/out`
   * Same directory on this server - `/var/lib/lhc-recordings`
   * LiveKit API URL - `http://127.0.0.1:7880` if Live Helper Chat runs on the same host
   * Retention in days and visitor notice text
5. Add cron job `php cron.php -s site_admin -c cron/voicevideo_recordings` once a day for retention.
6. Permissions: `lhvoicevideo` -> `record` (start/stop), `recordings` (play/download).

* Visitor sees the recording notice before joining and a `REC` indicator while recording.
* Recordings are streamed only through Live Helper Chat with chat access checks. Every play/download is written to the audit log (category `voice_call_recording`).
* Recording status, file size and duration arrive with LiveKit webhooks (`egress_*` events), webhook section in `livekit.yaml` is required.

## Call transfer

* Operator presses transfer button in the call window and picks an online operator.
* Chat transfer is created for the selected operator. The call continues until new operator opens the chat and joins the call. Previous operator is disconnected automatically.
* Call history gets separate records for both operators (end reason `transferred`).
* Permission `lhchat` -> `allowtransfer`.

## Supervisor step in

* In listen mode supervisor can press `Join the conversation`. Supervisor joins with microphone only, participants hear the supervisor and chat gets a message about it. Written to audit log.

## Security

* Access tokens are signed on Live Helper Chat server and issued per participant (`operator_<user_id>`, `visitor_<chat_id>`).
* Visitor receives token only after operator lets visitor in.
* Room names are derived from chat id and HMAC of chat hash with API secret, so they can not be guessed.
* Allowed publish sources follow configuration (camera only if video enabled, screen share only if screen share enabled).
* API secret is never printed back in the configuration form.

## Call lifecycle

* **Ring timeout** - if nobody lets the visitor in within configured seconds (default 60), the request is marked as not answered and the visitor is informed.
* **Operator ring tone** - the operator call window rings while a visitor is waiting to be let in.
* **Closed browser / lost connection** - the call window notifies the server on close, and LiveKit webhooks (`participant_left`, `room_finished`) end the call if a browser crashed or network dropped. Webhook requests are verified with the API secret.

## Supervisor silent monitoring

* Permission `lhvoicevideo` -> `supervise`.
* While a call is in progress, chat information panel shows `Listen to the call`.
* Supervisor joins with a listen only token (can not publish audio/video). Operator and visitor are not shown the supervisor.
* Every listen session is written to the audit log (category `voice_call_supervise`).

## Call history

`System configuration -> Voice & Video & ScreenShare -> Call history`

* Every call attempt is stored in `lh_chat_voice_video_session` with operator, department, provider, wait time, duration and end reason.
* Filters by date, department, operator, status, type and chat. CSV export.
* Operators see only calls from departments they have access to.
* Permission `lhvoicevideo` -> `sessions`.
* On call end a message with call type and duration is added to the chat (can be disabled in configuration).
* Events `voicevideo.call_ended` (`chat`, `session`) and `voicevideo.webhook` (`chat`, `call`, `event`) are dispatched for extensions.

## Scaling

A single LiveKit node handles hundreds of concurrent 1:1 calls. For more capacity run multiple nodes with shared Redis (see `redis` section in `livekit.yaml`).
