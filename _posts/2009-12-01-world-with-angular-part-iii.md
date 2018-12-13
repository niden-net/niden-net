---
layout: post
title: The world with Angular - Part III
tags: [amazon, angularjs, cloud computing, ec2, programming, review, how-to]
---

Continued from [Part II](/post/world-with-angular-part-ii)

##### Presentation of Data

Presenting data with `<angular/>` is really easy. All we need to do is to tell `<angular/>` how we want the data to be presented and where.
<img class="post-image" src="{{ site.baseurl }}/files/2009-12-01-angular.png" />

From [Part II](/post/world-with-angular-part-ii) you have seen that I have declared an entity called `Incident`. Also in the `<body>` tag I have initialized the `<angular/>` engine, requested `all()` the `Incident` objects and stored them in the `incidents` variable.

```html
<body ng-entity="incident=Incident" ng-init="incidents=Incident.all()">
```

I am creating a HTML table where I will display all the records. The header of the table is as follows:

```html
<tr>
  <th>&nbsp;</th>
  <th>Date</th>
  <th>Shift Start/End</th>
  <th>Animal Code</th>
  <th>Situation Code</th>
  <th>Resolution Code</th>
  <th>City</th>
  <th>State</th>
  <th>Description</th>
</tr>
```

Now I need to show the data on screen. I can reference the variables that I have used earlier in my input elements i.e. `incident.shiftstart`, `incident.shiftend`, `incident.animalcode` etc. The variables represent the fields of each record so `incident` is the document/record if you like and `animalcode` is the field. If I add them just like that, `<angular/>` will correctly show me data but only one record since it will not know otherwise. As usual I will need a loop for this. The loop will work directly on the `<tr>` definitions to give me a row per record.

```html
<tr ng-repeat="record in incidents">
  <td class="centercell" style="white-space:nowrap;">
    E D
  </td>
  <td class="centercell">{{record.shiftdate}}</td>
  <td class="centercell">{{record.shiftstart}}<br />{{record.shiftend}}</td>
  <td class="centercell">{{record.animalcode}}</td>
  <td class="centercell">{{record.situationcode}}</td>
  <td class="centercell">{{record.resolutioncode}}</td>
  <td class="centercell">{{record.city}}</td>
  <td class="centercell">{{record.state}}</td>
  <td>{{record.details}}</td>
</tr>
```

I am using the `ng-repeat` directive to repeat that block of code. Since I am using it in a `<tr>` the `ng-repeat` will match the closing tag for that element i.e. `</tr>`. `ng-repeat` repeats that block of code for every `record `in `incidents`. `incidents `is the variable I have stored all the results earlier from the `<body>` declaration. Each piece of information is stored in the database and referenced the same way that it was stored. Note that I have used the variable name `record `to access the loop elements but I could have used anything I liked. `record `refers to one object of the `incidents `variable at a time.

The full block of code is below:

```html
<table style='width:100%'>
<tr>
  <th>&nbsp;</th>
  <th>Date</th>
  <th>Shift Start/End</th>
  <th>Animal Code</th>
  <th>Situation Code</th>
  <th>Resolution Code</th>
  <th>City</th>
  <th>State</th>
  <th>Description</th>
</tr>
<tr ng-repeat="record in incidents">
  <td class="centercell" style="white-space:nowrap;">
    E D
  </td>
  <td class="centercell">{{record.shiftdate}}</td>
  <td class="centercell">{{record.shiftstart}}<br />{{record.shiftend}}</td>
  <td class="centercell">{{record.animalcode}}</td>
  <td class="centercell">{{record.situationcode}}</td>
  <td class="centercell">{{record.resolutioncode}}</td>
  <td class="centercell">{{record.city}}</td>
  <td class="centercell">{{record.state}}</td>
  <td>{{record.details}}</td>
</tr>
</table>
```

So now I have a basic application that allows me to store and display data. If you have tried this code so far and run it, you will see that the data is entered immediately on the screen and there are no page refreshes which significantly enhances the user experience.

You might have noticed that I have "E D" in the first column of every row of the HTML table. These are for `Edit` and `Delete`.

##### Editing Data

Each record has a unique identifier as I showed you earlier when adding records. In order for the "E" to link to the respective record, I need to use that id. The hyperlink around the "E" becomes then:

```html
<a href="#incident={{record.id}}">E</a>
```

Every document/record has an id field. I can it as usual with the double brackets and referencing the incident loop variable (inside the `<tr>` loop). Note that since I have decided to name my entity `incident`, I am referencing each record with that parameter in the URL. If my entity's name was different, say `testentity`, then the URL would have changed to:

```html
<a href="#testentity={{record.id}}">E</a>
```html

##### Deleting Data

To delete data, instead of referencing the unique identifier of each record, I will use the `record` variable and the `$delete()` method on it. Note that the `record `variable is the one that allows us to have access to every object in the `incidents `variable and it is used in the display data loop.:

```html
<a href="#" ng-action="record.$delete()">D</a>
```

##### Searching Data

The initial design never catered for emptying the database in regular intervals. Therefore a search function is in order to ensure quick retrieval of information. As you will see it is really easy and it does not need any `<form>` elements or anything else.

The search box is just an `<input>` element with a specific name (which can be whatever you like)

```html
[ Search: <input name="wrlfilter" /> ]
```

This piece of code appears above the table that displays data in my example. I need to make one more change to ensure that the data is filtered. The change is in the `<tr>` statement where I run the loop to display the data. So the block of code:

```html
<tr ng-repeat="record in incidents">
```

becomes

```html
<pre class="brush:html"><tr ng-repeat="record in incidents.$filter(wrlfilter)">
```

As you can see I am using the `$filter()` method and the parameter passed is the name of the input box that I have defined earlier. This way whatever I type in the search box, `<angular/>` will try to match it with the currently displayed data and filter accordingly, thus giving me the search functionality that I want.

##### Sorting data

Another easy task in `<angular/>` is sorting. Since I already have a table that I present data with, I am going to use that and its table headings to allow my user to sort. Also I am going to have a default sorting option of `Shift Date` descending and `Shift Start` descending.

In general sorting is done by the `$orderBy()`, `$orderByToggle()` and `$orderByDirection()` methods. The + or - prefixing the name of a field passed defines ascending or descending order. To create a compound sorting key with multiple fields I need to enclose the field names in quotes and separate them with commas.


First of all I need to initialize the sorting mechanism. The best place for that is the table that displays the data. Therefore I get:

```html
<table style='width:100%' ng-init="wrlorder=['-shiftdate','-shiftstart']">
```

This statement initializes the `wrlorder` variable to contain a `shiftdate` and a `shiftstart` field in descending order. Note that the `shiftdate` and `shiftstart` are the same names of the fields that I have used throughout this article.

I save my changes and reload the page but there is no sorting. I actually haven't told `<angular/>` what data to sort. Since the data that I want to sort are in the table, I will enhance my `$filter()` method with an `$orderBy()`. This is the <tr> statement that we have worked with earlier.

```html
<tr ng-repeat="record in incidents.$filter(wrlfilter).$orderBy(wrlorder)">
```

Save and refresh and voila! The results are sorted by `Shift Date` descending and `Shift Start` descending. If I enter a new record, it will be displayed on screen sorted in the correct position. Note that the statement above allows for a combination of methods to be run against the `incidents` variable (filter and sort).

For the purposes of this exercise and for debugging, I have also added the `wrlorder` variable next to the search box so that I know what my sort fields are.

```html
[ Search: <input name="wrlfilter" /> ] - [ Order: {{wrlorder}} ]
```

Refreshing the page shows me now

```html
- [ Order: -shiftdate, -shiftstart ]
```

I am nearly there. All I need to do now is to make the table headings clickable so that the data is sorted anyway I want to. I will use the `$orderByToggle()` and `$orderByDirection()` methods on the `wrlorder` variable. My table heading becomes:

```html
<th>&nbsp;</th>
<th>
    <a href="" 
       ng-action="order.$orderByToggle('shiftdate')" 
       ng-class="order.$orderByDirection('shiftdate')">Date</a>
</th>
<th>
    <a href="" 
       ng-action="order.$orderByToggle('shiftstart')" 
       ng-class="order.$orderByDirection('shiftstart')">Shift Start</a>/
    <a href="" 
       ng-action="order.$orderByToggle('shiftend')" 
       ng-class="order.$orderByDirection('shiftend')">End</a>
</th>
<th>
    <a href="" 
       ng-action="order.$orderByToggle('animalcode')" 
       ng-class="order.$orderByDirection('animalcode')">Animal Code</a>
</th>
<th>
    <a href="" 
       ng-action="order.$orderByToggle('situationcode')" 
       ng-class="order.$orderByDirection('situationcode')">Situation Code</a>
</th>
<th>
    <a href="" 
       ng-action="order.$orderByToggle('resolutioncode')" 
       ng-class="order.$orderByDirection('resolutioncode')">Resolution Code</a>
</th>
<th>
    <a href="" 
       ng-action="order.$orderByToggle('city')" 
       ng-class="order.$orderByDirection('city')">City</a>
</th>
<th>
    <a href="" 
       ng-action="order.$orderByToggle('state')" 
       ng-class="order.$orderByDirection('state')">State</a>
</th>
<th>
    <a href="" 
       ng-action="order.$orderByToggle('details')" 
       ng-class="order.$orderByDirection('details')">Description</a>
</th>
```

The `ng-action` allows for the interchange in sorting order (ascending/descending) while the `ng-class` shows a nice arrow next to the header name, indicating the current sort order.

Refreshing the page shows me the final product. I can now sort in any way I like and the sorting is compounded.

##### Widgets

`<angular/>` has a lot of widgets that can be used as validators but also as means to enhance the user experience. One of them is the DatePicker. I am going to use it to collect data in the `shiftdate` field. So the:

```html
<input name="incident.shiftdate" />
```

becomes:

```html
<input name="incident.shiftdate" ng-widget="datepicker" size="8" />
```

and that's it. Now when I click on the input box or when it gets focus, a nice dropdown calendar appears that allows me to select the date.


#### Final thoughts

The example above is not the final product for the WRL. There are some things missing, such as clearing up the order, enhancing the search, expanding/collapsing the add new record etc. This article is not meant as the final solution but more as a guide on what is feasible.

`<angular/>` is definitely a new way of looking at web programming. It is fast, agile and easy to learn. `<angular/>` provides the hobbyist a tool that they can use to create an online application that will suit their needs without complex installations, expensive hosting companies, RDBMS management etc. I can also see experienced programmers using it to address a quick fix or a very urgent requirement that demands RAD.

I encourage you to visit [getangular.com](http://www.getangular.com\) and give `<angular/>` a try. I am sure you will not be disappointed.
