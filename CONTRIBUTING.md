# Contributing to Dovedi #

Contributions to the Dovedi project are more than welcome.

## License ##

Dovedi is distributed under the terms of the GNU Public License (version 2 or newer). By contributing code, you agree to license your contribution under the same.

## Issues ##

Open a GitHub issue for anything. Don't worry if you find yourself opening something that sounds like it could be obvious; someone else might have the same question.

## Comments ##

Comment on any GitHub issue, open or closed. The only guidelines here are to be friendly and welcoming. If you see that a question has been asked and you think you know the answer, feel free to respond. No need to wait!

## Code Submission ##

### Work Flow ###

The **master** branch is the latest stable release.  The **develop** branch is where new features and on-going development take place.  The next stable release will come from the **develop** branch.

### Pull Requests ###

Submit a pull request at any time, whether an issue has been created or not. It may be helpful to discuss your goals in an issue first, though many things can best be shown with code.

Please submit pull requests against the **develop** branch from a feature branch in your fork of the project in GitHub. We ask that you test your code as we will also do our best to code review, test and verify that the pull request is as stable as possible before merging it.

### Hotfixes ###

If a bug fix or code change is deemed important enough that it should be fixed in the latest stable release, the pull request should be targeted at the **master** branch.

Once reviewed and tested, it will be merged and a new 'patch' release will be created.

After a release is created, any changes introduced into **master** that bypassed the **develop** branch should be merged into the **develop** branch.  This can be done in GitHub as a pull request.

## Code Style ##

### PHP ###

For any PHP, we try to follow the WordPress core [code standards](http://make.wordpress.org/core/handbook/coding-standards/).