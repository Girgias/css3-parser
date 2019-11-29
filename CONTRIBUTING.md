# Contributing

Contributions are warmly welcomed, to do so please first fork the project and write the feature/bug-fix/other
in a so called feature branch, i.e. ``git checkout -b feature-branch-name``.

To ensure the code passes CI you can use the composer commands down below.

In case of a feature addition, please insure that you add sufficient unit tests and/or integration tests.

In case of a bug fix, add a regression test case (if applicable) with your Pull Request.

In case your branch becomes out of sync, please rebase on top of master instead of merging master into your branch,
i.e. ``git rebase master``.

In case you have a lot of small fixup commits, I kindly ask you to rebase your branch interactively to merge
together those commits with the parent commit by using ``git rebase -i master`` and then replacing ``pick`` with
``fixup`` or ``f`` and then force push onto the branch. This is to prevent unnecessary commits.

## Composer commands
 * ``composer syntax``: Check if codebase follows coding style.
 * ``composer syntax-fix``: Fixes codebase style according to the defined coding styles.
 * ``composer static-analysis``: Run static analysis on codebase.
 * ``composer test``: Runs the test suite.
 * ``composer code-coverage``: Run test suite and provides code coverage in the CLI.
 * ``composer code-coverage-report``: Run test suite and generate a HTML code coverage report
 into ``./coverage/`` folder.
 * ``composer memory-test``: Execute Roave/no-leaks
 * ``composer ci``: Run Continuous Integration scripts, identical to the following commands chained:
 ``composer syntax``, ``composer static-analysis``, ``composer code-coverage``, ``composer memory-test``.
