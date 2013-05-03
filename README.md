# Lemur Learning

**Status: Beta**

An e-learning app for the [Elefant CMS](http://www.elefantcms.com/). Lemur aims
to be the Wordpress of e-learning, a simple way to publish and host your own
courses.

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

* Submitting feedback on responses
* Integrating SCORM data

Public-facing:

* Pay wall for paid courses
* Status indicator (course completion w/ link to next outstanding question)

Email notifications:

* To instructor for new assessment input
* To learner/instructor for assessment replies
* To instructor for new comments
* To instructor for new learner registered
* To learner welcome email, course subscribed confirmation/receipt

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

First, you will need to install the following Elefant apps that Lemur depends on:

* [Comments](https://github.com/jbroadway/comments)
* [SCORM](https://github.com/jbroadway/scorm)

Next, clone Lemur into your apps folder:

```
cd apps
git clone git://github.com/cewca/lemur.git
```

Finally, copy the `sample_bootstrap.php` file from your `apps/lemur` folder into
your site root as `bootstrap.php` (if the file `bootstrap.php` already exists,
simply copy the initialization code from `sample_bootstrap.php` into your
existing bootstrap file.

> **Optional:** Copy the `product.php` file from your `apps/lemur` folder into
> the global `conf` folder, overwriting the existing copy. This will replace the
> Elefant branding with Lemur's own.

Now log into Elefant and run the Lemur installer by navigating to Tools > Courses.
You should now have a working Lemur installation.

To create courses, go to Tools > Courses. To install SCORM modules for use in your
courses, go to Tools > SCORM. To view the list of courses on your site visit the
`/courses` URL and you will see any publicly visible courses listed there.

## Documentation

Early documentation is available through Lemur's [Github wiki page](https://github.com/cewca/lemur/wiki).