# Form Save Bundle

This contao module saves forms configured with the save form field to the `tl_form_save` table.

## Requirements

- Contao 5.0+
- PHP 8.1+

## Install

```BASH
$ composer require guave/formsave-bundle
$ php vendor/bin/contao-console contao:migrate
```

## Usage

Create a form that saves to the table `tl_form_save`.

Add a hidden field with the field name `alias` and a default value of your choice.
The value will be used to display the form in the exported CSV.

Every other field with a field name will be serialized into a `form_data` field in the database.

Within the form overview in Contao there will be a new button that allows you to download a CSV of the submissions.

There is also an optional Form Name Content Element, which you can add to the same page as the form.
This will override the `alias` from the form.
This is useful for when you have the same form for multiple uses.
This way you can differentiate the submitions in the CSV by the `alias`.
