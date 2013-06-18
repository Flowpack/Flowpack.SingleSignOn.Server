Single sign-on server
=====================

The `Flowpack.SingleSignOn.Server` package provides the components for implementing a custom single sign-on server.
This package should be installed in a Flow application that implements custom domain logic and authentication
configuration in a project specific package.

Requirements for a single sign-on server:

* Implementation of a Party domain model and domain logic
* Configuration of authentication
* Inclusion of routes for HTTP services
* Party and account management (optional)
* Basic user interface for login and display of messages (optional)

Components
----------

This is a schematic view of the single sign-on server components:

.. image:: Images/sso-server-detail.png
        :alt: The server in detail
        :width: 50%
        :align: center

.. index::
   single: Server; Key pair

Server key pair
    The server has a *public / private key pair*

.. index::
   single: Server; Service base URI

Service base URI
    The server exports HTTP services on a specific URL path. This path acts as the *Service base URI*
    (e.g. `http://ssoserver.local/sso/`) or *server identifier*.

.. index::
   single: Server; Clients

Clients
    All the instances have to be registered as a single sign-on client with their public key and service base URI. This
    allows for (signed) server-side requests initiated by the client or the server. The client public key restricts
    access to the single sign-on only to explicitly registered clients. The clients are persisted as entities inside a
    configured database. A management interface for the clients can be implemented in a custom package.

.. note:: The server uses the default Flow security framework for authentication during single sign-on requests. So a
   user that doesn't have an authenticated session on the server will be delegated to one of the configured
   authentication providers.

TODO Show usage of authentication provider and accounts on server

.. index::
   single: Server; Configuration

Configuration
-----------------------

.. index::
   single: Server; Commands

Commands
-----------------------

See Logging_.


.. index::
   single: Server; Logging

Logging
-----------------------

Client registration
-----------------------

Authentication endpoint
-----------------------

Client notification
-----------------------

HTTP services
-----------------------

Session synchronization
-----------------------

Account mapping
-----------------------

Account management API
-----------------------

