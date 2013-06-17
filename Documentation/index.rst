Flowpack Single sign-on Documentation
--------------------------------------------

*This version of the documentation has been rendered at:* |today|

======================================
Overview
======================================

The Flowpack Single sign-on packages provide a distributed authentication and authorization solution for
TYPO3 Flow applications. It is based on the Flow security framework and makes no special assumptions about the
actual authentication method, source of account data and the authorization data that is exchanged between the systems.

Architecture
=============

.. image:: Images/sso-overview.png
        :alt: An overview of the packages
        :width: 80%
        :align: center

Instance
    A (TYPO3 Flow) application that utilizes a Single sign-on server for authentication using the `Client package`_.
    The Flowpack Single sign-on can be one of multiple authentication methods. Usually there will be a larger number of
    instances in a typical setup.
Server
    A Single sign-on server is a TYPO3 Flow application that provides a central authentication system which is accessed
    by the instances. The server consists of the `Server package`_ and a domain package that implements a
    party model for the authentication and provides possible extensions to the Single sign-on data exchange.

.. _Client package: http://github.com/Flowpack/Flowpack.SingleSignOn.Client/
.. _Server package: http://github.com/Flowpack/Flowpack.SingleSignOn.Server/

The architecture is designed to be *highly extensible* and *fully integratable* in an existing TYPO3 Flow application.

Features
=============

* Easy integration into existing TYPO3 Flow applications
* Flow security framework integration, re-use of existing authentication providers (e.g. *LDAP*, *UsernamePassword*, *OpenID*)
* Flexible account data mapping (transfer custom properties of parties)
* Session expiration synchronization
* Remote session management capabilities
* Single Sign-off
* Account switching (impersonate)
* Sessions can use existing Flow cache backends (*Redis*, *Memcache*, *APC*)
* RSA signing of server-side requests

How it works
============

This is a simple roundtrip for access to a secured resource on an instance without prior authentication:

.. image:: Images/sso-roundtrip.png
        :alt: An overview of the packages
        :width: 80%
        :align: center

1. A user accesses a secured resource on an instance
2. Since no account is authenticated on the instance the user is redirected to a configured server
3. The user will authenticate on the server through a configured authentication provider (e.g. username / password)
4. The server redirects back to the instance and passes an encrypted access token
5. The instance checks the access token and does a server-side request to redeem the token on the server,
   the server returns the account data and authorization information (roles)
6. The instance authenticates an account locally and redirects to the original secured resource

======================================
Getting started
======================================

We provide TYPO3 Flow demo applications for both the server and an instance. To make the setup easier we
also provide the demo in a Vagrant_ box (a tool for a development environment in virtual machines).

.. WARNING:: Do not use the *Flowpack.SingleSignOn.DemoServer* package in production! It contains code that is used for
   testing and allows creation of users and session management over an unsecured HTTP API.

User accounts for testing:

======== ======== =============
Username Password Role
======== ======== =============
admin    password Administrator
user1    password User
user2    password User
======== ======== =============

Setting up the Vagrant demo
===========================

First install Vagrant_ for your operating system and install the `librarian` gem for downloading the bundled cookbooks::

    > librarian-chef install
      ...
    > vagrant up

The virtual machine should now boot and start to provision the demo setup (this can take a while).

Set up host entries in your `/etc/hosts` (or similar file, depending on your operating system)::

    10.11.12.23 ssodemoserver.vagrant
    10.11.12.23 ssodemoinstance.vagrant ssodemoinstance2.vagrant

Browse to http://ssodemoserver.vagrant/ and you should see the demo server frontend. A second instance is available on
http://ssodemoserver2.vagrant/ for running multi-instance acceptance tests.

.. _Vagrant: http://www.vagrantup.com/

Manually setting up the demo server and instance
================================================

The demo setup consists of a demo server and a demo instance bundled in two TYPO3 Flow distributions. You should follow
the steps in the `TYPO3 Flow quickstart`_ for a general setup for Flow development if not yet done.

Each distribution should be cloned into a separate directory::

    mkdir singlesignon-demo
    cd singlesignon-demo



*Setting up the server*

-----

Clone the repository, install dependencies with Composer::

    git clone https://github.com/Flowpack/Flowpack.SingleSignOn.DemoServer-Distribution.git DemoServer
    cd DemoServer
    path/to/composer.phar install --dev

Create a `Configuration/Settings.yaml`::

    TYPO3:
      Flow:
        persistence:
          backendOptions:
            dbname: ssodemoserver # Create this database
            host: localhost
            user: root   # Fill in username
            password: '' # Fill in password

    Flowpack:
      SingleSignOn:
        Server:
          server:
            serviceBaseUri: 'http://ssodemoserver.local/sso/'
            publicKeyFingerprint: ''

        DemoServer:
          demoInstanceUri: 'http://ssodemoinstance.local/'
          clients:
            -
              serviceBaseUri: 'http://ssodemoinstance.local/sso/'

Run migrations and demo setup::

    ./flow doctrine:migrate
    ./flow flowpack.singlesignon.demoserver:demo:setup


*Setting up the instance*

-----

Clone the repository, install dependencies with Composer::

    git clone https://github.com/Flowpack/Flowpack.SingleSignOn.DemoInstance-Distribution.git DemoInstance
    cd DemoInstance
    path/to/composer.phar install --dev

Create a `Configuration/Settings.yaml`::

    TYPO3:
      Flow:
        persistence:
          backendOptions:
            dbname: ssodemoinstance # Create this database
            host: localhost
            user: root   # Fill in username
            password: '' # Fill in password

    Flowpack:
      SingleSignOn:
        Client:
          client:
            serviceBaseUri: 'http://ssodemoinstance.local/sso/'
            publicKeyFingerprint: ''
          server:
            DemoServer:
              serviceBaseUri: 'http://ssodemoserver.local/sso/'
              publicKeyFingerprint: ''

        DemoInstance:
          demoServerUri: 'http://ssodemoserver.local/'


Run migrations and demo setup::

    ./flow doctrine:migrate
    ./flow flowpack.singlesignon.demoinstance:demo:setup

-----

You should create a virtual host configuration for both distributions. We expect the hosts `ssodemoinstance.local` and
`ssodemoserver.local` for the example configuration.

After setting up everyhting you should be able to access http://ssodemoserver.local/ and see the demo server frontpage.

.. _TYPO3 Flow quickstart: http://docs.typo3.org/flow/TYPO3FlowDocumentation/Quickstart/

Demo walkthrough
================



======================================
Usage
======================================

Setting up an SSO server
========================

Integrating the SSO client
==========================

======================================
Extensions
======================================

======================================
Development
======================================

Running the tests
=================

