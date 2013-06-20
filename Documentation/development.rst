Development
===========

Development of the Flowpack.SingleSignOn packages and distributions is coordinated on GitHub: http://github.com/Flowpack

Running the tests
-----------------

We have a test suite that covers all scenarios of the single sign-on with acceptance tests through Behat_ in the
TestSuite_ repository. The tests need a running demo setup with two different instances (configured via subcontexts).

*Install Behat via Composer:*

.. code-block:: bash

    $ git clone https://github.com/Flowpack/Flowpack.SingleSignon.TestSuite.git TestSuite
    $ cd TestSuite
    $ path/to/composer.phar install

The default `behat.yml.dist` configuration expects the demo installation with the URL `http://ssodemoinstance.dev/`,
`http://ssodemoinstance2.dev/` and `http://ssodemoserver.dev/`. A custom configuration for Behat can be used by copying
the file `behat.yml.dist` to `behat.yml`.

*Running the Behat tests:*

.. code-block:: bash

    $ bin/behat

This should execute all features and display the results of the scenarios.

.. _TestSuite: https://github.com/Flowpack/Flowpack.SingleSignon.TestSuite
.. _Behat: http://behat.org/
