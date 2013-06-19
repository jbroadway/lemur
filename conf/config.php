; <?php /*

[Lemur]

; A virtual page title to include in the navigation tree.
title = Courses

; Whether to include a virtual page in the navigation tree
; (see Tools > Navigation).
include_in_nav = "/courses"

; The public app name. Will appear as the
; page title at /lemur
public_name = Courses

; The layout to use for listings and other pages.
layout = default

; The layout to use for course content pages.
course_layout = default

; Enable/disable comments at the foot of course pages.
comments = On

[Admin]

handler = lemur/admin
name = Courses
install = lemur/install
upgrade = lemur/upgrade
version = 0.2-alpha

; */ ?>