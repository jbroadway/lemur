# Lemur REST API

The Lemur REST API can be accessed at `/lemur/api/*` or through the `lemur.js` script. Here is a list of the available methods.

## Category

### `POST category/order`

Update the order of categories.

Parameters:

* `order` - Array of category IDs in their new sorting order

Requires admin session.

### `POST category/update`

Update the name of a category.

Parameters:

* `id` - The category ID
* `title` - The updated category title

Requires admin session.

## Data

Learner input in Lemur is managed by the `lemur\Data` model.

### `POST data/submit/:id`

Submit an answer for a learner. Returns a `correct` parameter that will contain either `yes`, `no`, or `undetermined`. If the answer is incorrect, then the response will also include an `answer` parameter with the correct answer.

Parameters:

* `answer` - The learner's answer to the question.

Requires authenticated learner session.

## Item

Items are the sequential content that appears on each page of a course. An item can be a block of text, an image or video, a SCORM module, learner inputs, etc.

### `POST item/update_all`

Update all items for a page.

Parameters:

* `items` - Array of items with the following properties:
  * `id` - The item ID
  * `title` - The title of the item (optional)
  * `sorting` - The item sorting order
  * `content` - The content of the item
  * `answer` - The answer to an input item (optional)

Requires admin session.

### `POST item/create`

Create a new item.

Parameters:

* `title` - The title of the item (optional)
* `page` - The page ID to add the item to
* `type` - The item type (see `lemur\Item` model for a list of types)
* `sorting` - The item sorting order
* `content` - The content of the item
* `answer` - The answer to an input item (optional)

Returns the new item object, which also contains a `course` property and a new `id` value.

Requires admin session.

### `POST item/delete`

Delete an item.

Parameters:

* `item` - The item ID

Requires admin session.

## Learner

### `POST learner/add`

Add a learner to a course.

Parameters:

* `course` - The course ID
* `user` - The user ID of the learner

Requires admin session.

### `POST learner/remove`

Remove a learner from a course.

Parameters:

* `course` - The course ID
* `user` - The user ID of the learner

Requires admin session.

## Page

Courses in Lemur are composed of a series of pages, which contain a series of content items.

### `POST page/order`

Update the order of pages for a course.

Parameters:

* `course` - The course ID
* `order` - Array of page IDs in their new sorting order

Requires admin session.

### `POST page/update`

Update the name of a page.

Parameters:

* `id` - The page ID
* `title` - The updated page title

Requires admin session.
