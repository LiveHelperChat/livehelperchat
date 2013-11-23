<?php
/**
* Jaxl (Jabber XMPP Library)
*
* Copyright (c) 2009-2012, Abhinav Singh <me@abhinavsingh.com>.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
* notice, this list of conditions and the following disclaimer.
*
* * Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in
* the documentation and/or other materials provided with the
* distribution.
*
* * Neither the name of Abhinav Singh nor the names of his
* contributors may be used to endorse or promote products derived
* from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRIC
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
*/

// XML
define('NS_XML_pfx',                  "xml");
define('NS_XML',                      'http://www.w3.org/XML/1998/namespace');

// XMPP Core (RFC 3920)
define('NS_XMPP_pfx',                 "stream");
define('NS_XMPP',                     'http://etherx.jabber.org/streams');
define('NS_STREAM_ERRORS',            'urn:ietf:params:xml:ns:xmpp-streams');
define('NS_TLS',                      'urn:ietf:params:xml:ns:xmpp-tls');
define('NS_SASL',                     'urn:ietf:params:xml:ns:xmpp-sasl');
define('NS_BIND',                     'urn:ietf:params:xml:ns:xmpp-bind');
define('NS_STANZA_ERRORS',            'urn:ietf:params:xml:ns:xmpp-stanzas');

// XMPP-IM (RFC 3921)
define('NS_JABBER_CLIENT',            'jabber:client');
define('NS_JABBER_SERVER',            'jabber:server');
define('NS_SESSION',                  'urn:ietf:params:xml:ns:xmpp-session');
define('NS_ROSTER',                   'jabber:iq:roster');

// Stream Compression (XEP-0138)
define('NS_COMPRESSION_FEATURE',	  'http://jabber.org/features/compress');
define('NS_COMPRESSION_PROTOCOL',	  'http://jabber.org/protocol/compress');

?>
