# Excursions into Tate Gallery's Open Data

## VIEWS

[Graphs describing Tate purchases/gifts by year and artist gender](http://www.zenlan.com/tate/rickshaw.html)
[Lists detailing Tate purchases/gifts by year and artist gender](http://www.zenlan.com/tate/list.html)

## SOURCE

The data is sourced from the [Tate Collection repo](https://github.com/tategallery/collection)

## TOOLS

### SQL

tables.sql imports the Tate CSV data files, artist_data.csv and artwork_data.csv, into a MySQL database
views.sql creates views that

### APIs

PHP files that can be called to read and/or write the JSON files that power the views, returning the data JSONP wrapped.

api_ls.php feeds the list view.
api_rs.php feeds the graph view.

## Dependencies

[Rickshaw js/css](https://github.com/shutterstock/rickshaw) graphing library is included under the terms of the Rickshaw license. Please note that a very minor hack to rickshaw.js, Rickshaw.Graph.HoverDetail.initialize(), is in place in order to fix the format of hover details.

[D3](http://d3js.org/) is included as a dependency of Rickshaw.

[jQuery and jQueryUI](http://code.jquery.com) are linked to and not bundled.

## Links

[Twitter](http://twitter.com/zenlan)

[GitHub](http://github.com/zenlan)