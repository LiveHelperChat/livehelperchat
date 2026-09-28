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
| 5349 | TCP | TURN over TLS (optional, needs certificate, see `livekit.yaml`) |

## Install

1. Generate API key and secret

   ```
   docker run --rm livekit/livekit-server:v1.9 generate-keys
   ```

2. Replace `REPLACE_API_KEY`, `REPLACE_API_SECRET`, `turn.example.com` and the webhook URL (`chat.example.com`) in `livekit.yaml` and `livekit.example.com` in `Caddyfile`.
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
