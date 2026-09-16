<?php

namespace LiveHelperChat\Mcp\Session;

use LiveHelperChat\Models\Mcp\McpSession;
use Mcp\Server\Session\SessionStoreInterface;
use Symfony\Component\Uid\Uuid;

/**
 * MCP session store backed by the Live Helper Chat database (`lh_mcp_session`).
 *
 * The handshake era hands the client a session id during `initialize` and expects it back on every
 * following request, and the SDK answers `404 Session not found or has expired.` as soon as the id
 * is not in the store. PHP keeps no state between requests and a folder on disk is not shared
 * between the nodes of a load balanced setup, so the sessions live in the database instead - the
 * one place every node of an installation already agrees on.
 *
 * The table is part of the core database schema, so it is created by the regular system update
 * (`system/update` in the back office, `cron/util/update_database` from the command line) like any
 * other Live Helper Chat table.
 *
 * Every query is best effort: a failed query is reported as a missing session instead of an
 * exception, so a database hiccup cannot turn the endpoint into a PHP error page.
 */
class DatabaseSessionStore implements SessionStoreInterface
{
    /** Sessions not written for this many seconds are treated as expired - the SDK's default. */
    private $ttl;

    /** Set as soon as a query failed, so a missing table costs one query per request at most. */
    private $unavailable = false;

    /**
     * @param int $ttl Session lifetime in seconds.
     */
    public function __construct($ttl = 3600)
    {
        $this->ttl = (int)$ttl;
    }

    public function exists(Uuid $id): bool
    {
        return $this->sessionRow($id) !== false;
    }

    public function read(Uuid $id): string|false
    {
        $row = $this->sessionRow($id);

        if ($row === false || !isset($row->data) || $row->data === '') {
            return false;
        }

        return (string)$row->data;
    }

    public function write(Uuid $id, string $data): bool
    {
        if ($this->unavailable) {
            return false;
        }

        try {
            $now = time();

            $session = new McpSession();
            $session->session_id = $id->toRfc4122();
            $session->ctime = $now;
            $session->data = $data;
            $session->utime = $now;

            // Written through the persistent session directly: a session write must not expire the
            // site caches the way a regular model save does. `ctime` stays with the row, so only
            // the payload and `utime` change on a rewrite.
            $this->persistentSession()->saveOrUpdate($session, array('ctime'));

            return true;
        } catch (\Throwable $e) {
            $this->unavailable = true;

            return false;
        }
    }

    public function destroy(Uuid $id): bool
    {
        if ($this->unavailable) {
            return true;
        }

        try {
            $session = $this->sessionRow($id);

            if ($session !== false) {
                $this->persistentSession()->delete($session);
            }

            return true;
        } catch (\Throwable $e) {
            $this->unavailable = true;

            return true;
        }
    }

    public function gc(): array
    {
        if ($this->unavailable) {
            return array();
        }

        try {
            // The session payload is not needed to drop a row, so it is not read.
            $expired = McpSession::getList(array(
                'limit' => 500,
                'sort' => false,
                'filterlt' => array('utime' => time() - $this->ttl),
                'ignore_fields' => array('data'),
            ));

            $deleted = array();
            $persistentSession = $this->persistentSession();

            foreach ($expired as $session) {
                $persistentSession->delete($session);

                try {
                    $deleted[] = Uuid::fromString($session->session_id);
                } catch (\Throwable $e) {
                    // Not a UUID - nothing to report, the row is gone either way.
                }
            }

            return $deleted;
        } catch (\Throwable $e) {
            $this->unavailable = true;

            return array();
        }
    }

    /**
     * Loads a single, not yet expired session row.
     *
     * @return object|false
     */
    private function sessionRow(Uuid $id)
    {
        if ($this->unavailable) {
            return false;
        }

        try {
            $session = McpSession::findOne(array(
                'sort' => false,
                'filter' => array('session_id' => $id->toRfc4122()),
            ));

            if ($session === false || (int)$session->utime + $this->ttl < time()) {
                return false;
            }

            return $session;
        } catch (\Throwable $e) {
            $this->unavailable = true;

            return false;
        }
    }

    /**
     * @return \ezcPersistentSession
     */
    private function persistentSession()
    {
        return McpSession::getSession();
    }
}
