Category-Field-for-EE
=====================
Author: Nuno Albuquerque [http://www.nainteractive.com]

This field type allows you organize your category groups as you would any other custom field type anywhere on your publish entries form. Additionally a filtered input is provided for long category lists.

Release Notes
--------
1.5.3.1 (2013-10-12)
- Improved	- Error notice if field group's channel(s) have no assigned category groups.
- Bug fix 	- If field group was assigned to multiple channels, only one channel's assigned categories would display
- Bug fix	- Fixed database issues under certain versions of MySQL
- Bug fix	- Moved filter back to top of list


1.5.1	(2013-10-2)
- Bug Fix	- Database error when creating new a new fieldtype

1.5	(2013-09-30)

- New		- Added "Hide edit link" option in field settings screen.
- Improved  - Category group drop down only shows groups which have been assigned to the field's parent channel.

1.4.5 (2013-06-11)

- Bug Fix - Compatability with EE 2.7

1.4.4 (2013-06-06)

- Improved	- Better keyboard support for drop down select lists.

1.4.3 (2013-05-10)

- Update	- Moved JS to footer to follow EE 2.6 guidelines.

1.4.2 (2013-03-19)

- New 		- Added "select" option as default for drop down list.
- Bug fix 	- now honors required fieldtype setting

1.4.1 (2012-12-21)

- Bug fix 	- where under specific scenarios the edit link would not properly display
- Bug fix 	- js error when no category group is selected

1.4 (2012-12-18)

- Bugfix	- Conflict with channel images / Updated field settings save functions

1.2 (2012-10-23)
- (New) now supports displaying category group as select list

1.1 (2012-10-19)
- (New) now renders group_id in templates

1.0
2012-10-16 - Initial release.

Benefits
--------

- Place category groups logically within a publish entries page
- Make category selections required
- Add instructions for each category group
- Real time input filter (when list contains more than 10 items)
- Can display categories as select list for single choice category selection.
- Categories Edit link is placed directly below each category group vs. the bottom of the categories tab.
- No additional queries performed -- all the action happens on the publish entries form using the DOM