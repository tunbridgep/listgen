## List Generator

Generates item lists based on markdown input files.

## Features

Each list item can have the following features:

- Can specify whether or not it has a checkbox. Checkbox state is saved in browser storage
- Can easily shorthand links eg to a central wiki of information
- An optional tag. Checkboxes with the same tag will all be synchronised
- A "Read only" mode which will show a green checkmark or a red cross if all checkboxes with a certain tag are checked
- The ability to only show checkboxes matching a certain filter
- A few other things

## Usage

create a file called config.php. An example is provided below

    <?php
	$title = "Dark Souls 3 Cheat Sheet";
	$desc = "Cheat Sheet for Dark Souls 3. Checklist of things to do, items to get etc.";
	$long_desc = "The following is a checklist and set of information I use when playing Dark Souls III to make sure I don't miss
    	an item, conversation or boss. I hope you find it useful. A big thanks goes out to the community
    	of contributors on the <a href=\"http://darksouls3.wiki.fextralife.com/Dark+Souls+3+Wiki\">Dark Souls 3 Wiki</a> where
    	some of this information is borrowed from.
	$long_desc_subtitle = "Warning: Contains Spoilers";
	$default_link_location = "http://darksouls3.wiki.fextralife.com/";
	$default_link_space_character = "+";
	$output = "darksouls3.html";
	$template = "template/template.in"; # You shouldn't need to modify this
	#$standalone = true;
	AddFilter("Soul Items","Soul");
	AddFilter("NG+","NG+");
	AddFilter("NG++","NG++");
	?>

After this, create a *sections* directory in the project root. Any folders placed inside the sections directory will be interpreted as a new tab. Any md files placed inside these subfolders will be interpreted as sections within that tab.

Each section will have it's own completion tally, and tabs can be organised using a numeric prefix, which will be ignored when displaying the tab.

For instance, manually ordering 2 non-alphabetical tabs can be done like this

**000Completion/**
**001Accessing_Firelink_Shrine/**

Which will be displayed as:

**Completion**
**Accessing the Firelink Shrine**

markdown files can be organised and named the same way.

For an example set of config and section files, please see [this repository](https://bitbucket.org/plausiblesarge/listgen-data/src/master/)

After this, run generate.php using the php command line. A sample make.cmd file is provided for running this on windows. The command is:

**php generate.php**

## Using links

Normal markdown links are supported. Links can also use a shorthand designed to make repeatedly linking to the same website easier. By creating a link to (@x), this will create a link to the URL specified in **$default_link_location**, replacing all spaces with the **$default_link_space_character**

For example, if I have *darksouls.com/* as my default link location and *+* as my default space character, I can make a link like this:

	- Collect the [Estus Flask](@x) from the enemy in the next hallway
	
Which will be identical to creating the link manually:

	- Collect the [Estus Flask](darksouls.com/Estus+Flask from the enemy in the next hallway


The cheat sheet is very losely designed around the visual style of [The Dark Souls Cheat Sheet](http://smcnabb.github.io/dark-souls-cheat-sheet/) and was originally designed to easily create custom cheat-sheets for other games. However, it can be used to make anything.

## Credits

This project uses [Parsedown](https://github.com/erusev/parsedown) to process markdown files