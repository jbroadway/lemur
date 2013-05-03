# Lemur Learning

An e-learning app for the [Elefant CMS](http://www.elefantcms.com/).

**Status: Beta**

Course building and management is complete, learner management is
mostly complete, and basic course publishing works, with learner
input and registration also underway.

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