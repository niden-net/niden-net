---
layout: post
title: Uploading coverage reports to Codecov.io and Codacy
date: 2022-11-26T21:07:57.806Z
tags:
  - php
  - codecov
  - codacy
  - testing
---
Code coverage is a very informative feature of testing. For PHP, PHPUnit offers functionality to run tests as well as gather coverage statistics. 

<!--more-->

### Code Coverage

Code coverage is a report that a testing suite creates, that presents all the code and how many times each line has been executed, when running the testing suite. With this report, a developer can write additional tests to cover different paths of the application. Having high code coverage for an application offers a sense of security that the application will be relatively bug free. However, one can fall in the trap of *only* checking the code coverage and not following [DRY](https://en.wikipedia.org/wiki/Don't_repeat_yourself) or [SOLID](https://en.wikipedia.org/wiki/SOLID) coding techniques, which will streamline and simplify the said application.

For Phalcon, we have been using [Codeception](https://codeception.com) for a long time now, and have been utilizing the library's ability to run the tests and also produce code coverage reports.

The problem we have is that [Phalcon](https://phalcon.io) is an extension and as such, code coverage reports are not available as one would expect. There is a way to collect this data and push it to [Codecov](https://codecov.io) (showing coverage for C files) but that has been quite cumbersome to setup and use.

![Codacy Code Quality](/assets/files/20221126-codacy-quality.svg)

![Codacy Code Coverage](/assets/files/20221126-codacy-coverage.svg)

Since I am also working on [Phalcon v6](https://github.com/phalcon/phalcon), which is a pure PHP implementation, I have enabled Code coverage for the project and so far we are in a good path. The code is mostly rated at "A" and the code coverage is at 81%. When time allows, I will work more to increase the code coverage.

### GitHub Actions

The testing suite is running on GitHub Actions, and as a result, we can have asynchronous runs for each environment and testing suite. Each report is collected by the relevant action step and then uploaded as an artifact to the GitHub Actions artifact store.

Since there are different sets of tests with different environment settings, the testing suite cannot run with one command, generate the coverage file and then that file can be uploaded as one. There are several code coverage files that need to be uploaded in [Codacy](https://codacy.com) or [CodeCov](https://codecov.io).

For example, some tests need to run only on MySQL and to do so I need to run `vendor/bin/codecept` with specific parameters so that the environment is set up for those tests.

```bash
vendor/bin/codecept run tests/database -g mysql --env mysql
```

The above command loads the MySQL environment by setting the database up and then runs all the tests that have the `@group mysql` annotation. The same happens with PostgreSql and Sqlite tests.

### Collecting Reports

Each testing suite is run with the relevant command and a `coverage.xml` file is generated. The file then is uploaded in the GitHub Actions artifacts store, so that we can retrieve it later on. The command is:

```bash
vendor/bin/codecept run --coverage-xml=coverage.xml --ext DotReporter unit
```

This will create a file called `coverage.xml` in the `tests/_output` folder. I also use the `--ext DotReporter` to reduce the output on screen when tests run.

### Uploading Artifacts

As mentioned above, GitHub Actions is executing the testing suite. The workflow has several steps and each step is run in its own container. The coverage file generated is then uploaded to the Artifact store

```yml
- name: "Run Unit Tests"
  if: always()
  run: |
    vendor/bin/codecept run --coverage-xml=coverage.xml --ext DotReporter unit

- name: "Upload coverage file artifact"
  uses: "actions/upload-artifact@v3"
  with:
    name: "unit-${{ matrix.php }}-${{ matrix.ts }}-${{ matrix.name }}.coverage"
    path: "tests/_output/coverage.xml"
```

The first step shown above is running all the unit tests. The output file will be `coverage.xml`. Once that is completed, the `actions/upload-artifact` action is used, to upload the file to the Artifact store. The `coverage.xml` file will be zipped and then uploaded. The `path` line defines which file to upload and the `name` is the name that will be used when stored in the Artifact store. 

For the `unit` tests, I am using a matrix, running the tests in different PHP versions (8.0 and 8.1) but also different environments (Linux, macOS and Windows) as well as thread safe and non thread safe. For the above step, when the coverage reports are generated, the files will be named:

```bash
unit-8.0-nts-macos-clang.coverage
unit-8.0-nts-ubuntu-gcc.coverage
unit-8.0-nts-windows2019-vs16.coverage
unit-8.0-ts-macos-clang.coverage
unit-8.0-ts-ubuntu-gcc.coverage
unit-8.0-ts-windows2019-vs16.coverage
unit-8.1-nts-macos-clang.coverage
unit-8.1-nts-ubuntu-gcc.coverage
unit-8.1-nts-windows2019-vs16.coverage
unit-8.1-ts-macos-clang.coverage
unit-8.1-ts-ubuntu-gcc.coverage
unit-8.1-ts-windows2019-vs16.coverage
...
```

The code as is now does not have any conditionals that will run a specific method or line of code depending only in the architecture of the system. Therefore, all the unit tests are producing the same coverage report. This might seem as an overkill, but it was done intentionally, in the case that we do make changes to the framework to utilize methods available in a particular architecture or operating system in the future.

### Uploading Reports

The upload process to [Codacy](https://codacy.com) and [CodeCov](https://codecov.io) is also a step in the workflow. First, all the reports are downloaded from the artifact store and then one step uploads them to [Codacy](https://codacy.com) and another step to [CodeCov](https://codecov.io).

```yml
name: "Upload coverage to Codecov/Codacy"
runs-on: "ubuntu-22.04"
needs:
  - "unit-tests"
  - "cli-tests"
  - "integration-tests"
  - "db-mysql-tests"
  - "db-sqlite-tests"
```
The above defines the prerequisites for the run. The upload will not run unless all the above steps have completed successfully, ensuring that resources are not wasted for unsuccessful runs.

```yml
steps:
  - name: "Checkout"
    uses: "actions/checkout@v3"
    with:
      fetch-depth: 2

  - name: "Display structure of downloaded files"
    run: |
      mkdir -p reports

  - name: "Download coverage files"
    uses: "actions/download-artifact@v3"
    with:
      path: "reports"

  - name: "Display structure of downloaded files"
    run: ls -R
    working-directory: reports
```
The steps above are pretty explanatory. The code is checked out, the artifacts are downloaded to the `reports` folder and then a listing of the reports is shown on the terminal. As soon as the reports are downloaded, they are also unzipped in their respective folders. The output looks like this:


```bash
...
unit-8.0-nts-macos-clang.coverage
unit-8.0-nts-ubuntu-gcc.coverage
unit-8.0-nts-windows2019-vs16.coverage
...

./unit-8.0-nts-macos-clang.coverage:
coverage.xml

./unit-8.0-nts-ubuntu-gcc.coverage:
coverage.xml

./unit-8.0-nts-windows2019-vs16.coverage:
coverage.xml
...
```

Now that the files are all in the `reports` folder, uploading to [Codacy](https://codacy.com) and [CodeCov](https://codecov.io) is easy with the following steps:

```yml
- name: "Upload to Codecov"
  uses: "codecov/codecov-action@v3"
  with:
    token: ${{ secrets.CODECOV_TOKEN }}
    directory: reports
    fail_ci_if_error: true
    verbose: true

- name: "Upload to Codacy"
  env:
    project-token: ${{ secrets.CODACY_PROJECT_TOKEN }}
  run: |
    bash <(curl -Ls https://coverage.codacy.com/get.sh) report \
      -l PHP $(find ./reports/ -name 'coverage.xml' -printf '-r %p ')
```

The first action is provided by Codecov and I only need to specify the folder where the coverage data is located (`reports` in my case). It will traverse the folder and its subfolders, find the `xml` files and upload them. Also note that O jave set tje `fail_ci_if_error` to `true` to ensure that all the steps complete successfully.

The second action for Codacy is using `bash` and Codacy's uploader with a bit of `bash` magic. The `find` command is used to locate all the reports and the uploader does the rest.

### Conclusion

Enabling Code Coverage reporting for your PHP project is a great indicator on what your tests are testing. The report can easily point out application paths that have not been executed, so that relevant tests can be written to execute such paths. The higher the coverage, the more confidence the developer has that the application behaves as it should. 

Having 100% code coverage does not mean that there are no bugs in the application tested. What one does get out if it is the assurance that any change in the future will provide an accurate report (through tests) of the impact the change has throughout the application. The all to common _you fixed something but broke something else_

Finally, the Code Coverage report should not be seen as the Holy Grail of testing. If, as a developer, you end up spending hours and hours trying to increase your code coverage by 0.1% then you might want to rethink where you spend your time and how productive that is.

In the ideal world, one should have 100% code coverage **as the application is being built**, but then again we do not live in an ideal world...


> Note: The complete workflow is located in the [Phalcon v6](https://github.com/phalcon/phalcon) repository.
{: .alert .alert-info }
