Overview
========

The Flowpack Single sign-on is designed for integration in TYPO3 Flow applications and offers a full single sign-on
solution without the need of external components. The `Server package`_ can be used to build a custom authentication
server based on TYPO3 Flow while the `Client package`_ can be used to integrate existing TYPO3 Flow applications into
the single sign-on.

We designed the solution for ease of use and a seamless authentication experience for the user.

Features
--------

* Easy integration into existing TYPO3 Flow applications
* Flow security framework integration, re-use of existing authentication providers (e.g. *LDAP*, *UsernamePassword*, *OpenID*)
* Flexible account data mapping (transfer custom properties of parties)
* Session expiration synchronization
* Remote session management capabilities
* Single Sign-off
* Account switching (impersonate)
* Sessions can use existing Flow cache backends (*Redis*, *Memcache*, *APC*)
* RSA signing of server-side requests

Architecture
------------

.. image:: Images/sso-overview.png
        :alt: An overview of the packages
        :width: 80%
        :align: center

The architecture is designed to be *highly extensible* and *fully integratable* in an existing TYPO3 Flow application.

Server
^^^^^^

A Single sign-on server is a TYPO3 Flow application that provides a central authentication system which is accessed
by the instances. The server consists of the `Server package`_ and a domain package that implements a
party model for the authentication and provides possible extensions to the Single sign-on data exchange.

.. image:: Images/sso-server-detail.png
        :alt: The server in detail
        :width: 50%
        :align: center

The server has a *public / private key pair* and exports HTTP service as the *Service base URI*
(e.g. `http://ssoserver.local/sso/`). The service base URI is also used as the unique server identifier.

All the instances have to be registered as a single sign-on client with their public key and service base URI. This
allows for (signed) server-side requests initiated by the client or the server. The client public key restricts
access to the single sign-on only to explicitly registered clients. The clients are persisted as entities inside a
configured database. A management interface for the clients can be implemented in a custom package.

See :doc:`sso-server` for more information about implementing a custom server application.

Instance
^^^^^^^^

An instance is a (TYPO3 Flow) application that utilizes a Single sign-on server for authentication using the `Client package`_.
The Flowpack Single sign-on can be one of multiple authentication methods on the instance. Usually there will be a
larger number of instances in a typical setup.

.. image:: Images/sso-client-detail.png
        :alt: The client in detail
        :width: 50%
        :align: center

The single sign-on client on the instance has a public / private key pair and a *Service base URI* as a unique
client identifier. The client needs at least one configured single sign-on server with the server public key and
service base URI. The client is used by the instance through the Flow security framework as a special
authentication provider.

Authentication round trip
-------------------------

This is a simple round trip for access to a secured resource on an instance without prior authentication:

.. image:: Images/sso-roundtrip.png
        :alt: An overview of the packages
        :width: 80%
        :align: center

1. A user accesses a secured resource on an instance
2. Since no account is authenticated on the instance the user is redirected to a configured server
3. The user will authenticate on the server through a configured authentication provider (e.g. username / password)
4. The server redirects back to the instance and passes an encrypted access token
5. The instance decrypts the access token and does a server-side request to redeem the token on the server,
   the server verifies the token and returns the account data and authorization information (e.g. roles)
6. The instance authenticates an account locally and redirects to the original secured resource

.. _Client package: http://github.com/Flowpack/Flowpack.SingleSignOn.Client/
.. _Server package: http://github.com/Flowpack/Flowpack.SingleSignOn.Server/
