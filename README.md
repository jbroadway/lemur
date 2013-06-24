# Lemur Learning

**Status: Beta**

An e-learning app for the [Elefant CMS](http://www.elefantcms.com/). Lemur aims
to be the Wordpress of e-learning, a free and ultra-simple way to publish and
host your own courses that keeps you in control.

However, Lemur has a ways to go before it is ready for non-developers. We still
have a number of features to finish, reams of documentation to write, and more
work to do to make it as simple as possible to get up and running, regardless
of your skill level.

Lemur Learning is brought to you by the [Centre for Education and Work](http://www.cewca.org/),
a Canadian non-profit dedicated to helping Canadians improve their lives
through learning.

Here is a screenshot of the Lemur Learning course editor:

![Lemur Learning course editor](https://raw.github.com/cewca/lemur/master/pix/screenshot-editor.png)

## Features

* Publish your own courses of any length
* Easy-to-use and powerful course editor
* Embed SCORM modules and other dynamic content into courses
* Courses can be free, paid, or private
* Easy learner account management
* Learner input and instructor feedback cycle
* Built on a fast, completely modern CMS platform
* Easy theming of your learner website
* Integrate with the Lemur Learning API

## To do

Backend:

* Submitting instructor feedback on responses
* Integrating SCORM data

Email notifications:

* To instructor for new assessment input
* To instructor for new comments
* To instructor for new learner registered
* To learner welcome email

Documentation:

* Installing Lemur Learning
* Building courses
* Managing learners
* Managing discussions and assessments
* Adding custom content types to page editor
* How to embed and integrate external content
* How to use the Lemur Learning API
* How to contribute to the project

## Installation

First, you will need to install the [Elefant CMS](http://www.elefantcms.com/download).
Once that is running, follow these steps:

1\. From the root folder of the site run the following command:

```bash
php composer.phar require elefant/app-lemur
```

This will also install the following apps that Lemur depends on:

* [Comments](https://github.com/jbroadway/comments)
* [SCORM](https://github.com/jbroadway/scorm)
* [Stripe Payments](https://github.com/jbroadway/stripe)

> **Note:** You may need to add `"minimum-stability": "dev"` to your `composer.json`
> file in order for Composer to work correctly while Lemur is still in development.

> **Payments:** Additional payment providers can be supported by implementing the
> [payment handler interface found here](https://github.com/jbroadway/stripe#creating-a-member-payment-or-subscription-form).
> More documentation and examples still to come.

2\. Copy the file `apps/lemur/sample_bootstrap.php` into your document root and rename
it `bootstrap.php`. If a `bootstrap.php` already exists, open the file and add the
relevant lines of code to your existing `bootstrap.php` file.

```bash
cp apps/lemur/sample_bootstrap.php bootstrap.php
```

3\. Copy the `apps/lemur/theme` folder into your `layouts` folder and rename it `lemur`.

```bash
cp -R apps/lemur/theme layouts/lemur
```

4\. Log into Elefant and run the Lemur installer by navigating to Tools > Courses.

### Optional steps

5\. Go to Tools > Navigation and add the `Courses` page to your site tree.

6\. Go to Tools > Designer and set the Lemur layout as your default.

7\. Copy the `product.php` file from your `apps/lemur` folder into the global
`conf` folder, overwriting the existing copy. This will replace the Elefant
branding with Lemur's own.

You should now have a working Lemur installation.

## First steps

To create courses, go to Tools > Courses. To install SCORM modules for use in your
courses, go to Tools > SCORM. To view the list of courses on your site visit the
`/courses` URL and you will see any publicly visible courses listed there.

## Documentation

Early documentation is available through Lemur's [Github wiki page](https://github.com/cewca/lemur/wiki).