## AngularJS - Simplicity in your browser

<img class="post-image" src="{{ cdnUrl }}/files/angularjs.png" />

Recently I was contacted by an acquaintance through my Google+ circles, who needed some help with a project of hers.

Her task was to redesign a church website. Pretty simple stuff, CSS, HTML and content.

### Scope
The particular church videotapes all the sermons and posts them on their channel in [LiveStream](http://www.livestream.com/) for their followers to watch. One of the requirements was to redo the video archives page and to offer a link where followers can download the audio of each sermon for listening.

### Design (kinda)
After the initial contact, I decided to get rid of all the bloated [jQuery](http://www.jquery.com/) code that was there to control the video player and use [AngularJS](http://angularjs.org/) to control the generation of content. There were two key facts that influenced my decision:

* the use of `ng-repeat` to generate the table that will list all the available sermons and
* the variable binding that [AngularJS](http://angularjs.org/) offers to play the video in the available player.

I also decided to switch the player to a new updated one that [LiveStream](http://www.livestream.com/) offered, which features a slider to jump through the video, volume control and more.

### Previous code
The previous code for that page was around 300 lines. The file had some CSS in it, quite a few lines of HTML but was heavy on javascript. There were a lot of [jQuery](http://www.jquery.com/) functions which controlled the retrieval of the available videos per playlist. Each playlist would be effectively a collection of videos for a particular year. [jQuery](http://www.jquery.com/) was observing clicks on specific links and make an AJAX call to the [LiveStream](http://www.livestream.com/) API to retrieve the list of available data in JSON format, and output the formatted results (as a table) on screen. It was something like this:

```js
head
title
link - css
style (inline)
....
end inline style
script jQuery
script jQueryUI
script jquery.timer
link jquery-ui CSS
link slider CSS
style (inline)
....
end inline style
script jQuery
$(document).ready ... // Document ready
....
$("#div_col dd").click // Bind click to a year
.....
getPlaylists(year) // Not sure why this was ever here, not used
....
getPlaylistClips(playlistID) // Gets the clips of the playlist
.....
playClip(clipID) // Plays the clip in the player
.....
end jQuery script
script Video Player
....
end head
body
navigation
main container
list of years
instructions
container to show video player
container to show video list
end body
end html
```

### Enter AngularJS
I checked the latest video player from [LiveStream](http://www.livestream.com/). The code was much cleaner and all I had to do is bind one variable, the GUID of the video, in the relevant call so that the video can be played. I also bound another variable (the video title) above the video so as to offer more information to the user.

With a bit of Bootstrap CSS, I created two tabs and listed the two years 2012, 2011. A function was created in my [AngularJS](http://angularjs.org/) module to accept the year and make the relevant call to the [LiveStream](http://www.livestream.com/) API to receive the data a a JSON object.

`ng-repeat` (with `>ng-cloak`) was used to "print" the data on screen and the application was ready.

I removed all the cruft and created one small file that is loaded and offers the functionality that we need. It is 50 lines of code (just the javascript part. The code is below with added comments for the reader to follow:

```js
// Create the module and inject the Resource object
var ngModule = angular.module("CHF", ['ngResource']);

// The main controller that needs the scope and resource
ngModule.controller("MainCtrl", function ($scope, $resource) {

    // Calculates the current year
    //  ensures we always get the last year on first load
    $scope.currentYear = function () {
        var currentDate = new Date();
        return currentDate.getFullYear();
    };
    
    // This is the playlist array. This is obtained by 
    //  LiveStream and it changes once every year. 
    //  Hardly an effort by the administrator
    $scope.playlists = [
        {"year":"2012", "guid":"63426-xxx-xxx-xxx"},
        {"year":"2011", "guid":"84f84-xxx-xxx-xxx"}
    ];

    // This couldn't be simpler. It merely sets some variables 
    //  in the scope. By doing so, the binding in the relevant
    //  variables will allow the video to play and the title 
    //  to update.
    $scope.playVideo = function (element) {
        $scope.currentVideo  = element.guid;
        $scope.currentTitle  = element.title;
    };

    // This is the core. It makes the AJAX request to the 
    // LiveStream API so that it can get the JSON data back
    $scope.makeRequest = function (year) {
        
        // Calculating the current year and the year selected.
        // Their difference offers an offset which effectively 
        // is the offset of the array stored in $scope.playlists
        var thisYear    = $scope.currentYear();
        var diff        = thisYear - year;

        var objData = $scope.playlists[diff];

        // Just in case something was passed that is not valid
        if (objData.guid)
        {
            var reqData = $resource(
                "http://livestream_url/2.0/:action",
                {
                    action:'listclips.json', 
                    id:objData.guid,
                    query: {isArray: true},
                    maxresults:"500",
                    callback:'JSON_CALLBACK'
                },
                {get:{method:'JSONP'}}
            );

            // Set the year and get the data
            $scope.year    = year;
            $scope.listData = reqData.get();
       }
    };

    // This is the first load - load the current year
    $scope.makeRequest($scope.currentYear());

});
```

Now moving into the HTML side of things:

```html
<div id="playerContainer" style='text-align:center;'>
    <p ng-cloak>
        {{currentTitle}}
    </p>
    <iframe 
        width="560" 
        height="340" 
        src="http://ls_url?clip={{currentVideo}}&amp;params" 
        style="border:0;outline:0" 
        frameborder="0" 
        scrolling="no">
    </iframe>
</div>

<br />
<div>
    <ul class="nav nav-tabs">
        <li ng-repeat="playlist in playlists" 
               ng-class="{true:'active',false:''}[year == playlist.year]">
            <a ng-click="makeRequest(playlist.year)">{{playlist.year}}</a>
        </li>
    </ul>
    <table class="table table-bordered" style="width: 100%;">
        <thead>
            <tr>
                <th>Date/Title</th>
                <th>Audio</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-cloak ng-repeat="video in listData.channel.item">
                <td ng-click="playVideo(video)">{{video.title}}</td>
                <td>
                    <span ng-show="video.description">
                        <a href="{{video.description}}" title="Download Audio">
                            <i class="icon-download-alt"></i>
                        </a>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

That is all the HTML I had to change. The full HTML file is 100 lines and 50 for the [AngularJS](http://angularjs.org/) related javascript, I can safely say that I had a 50% reduction in code offering the same functionality - and if I might say so, it is much much cleaner.

The final page looks something like this:

![]({{ cdnUrl }}/files/2012-09-02-sermon.png)

### Pointers

```html
<div id="playerContainer" style='text-align:center;'>
    <p ng-cloak>
    {{currentTitle}}
    </p>
    <iframe 
        width="560" 
        height="340" 
        src="http://ls_url?clip={{currentVideo}}&amp;params" 
        style="border:0;outline:0" 
        frameborder="0" 
        scrolling="no">
    </iframe>
</div>
```

This block displays the video player and due to the variable binding that AngularJS offers, the minute those variables change, the video is ready to be played.

```html
<ul class="nav nav-tabs">
    <li ng-repeat="playlist in playlists" 
           ng-class="{true:'active',false:''}[year == playlist.year]">
        <a ng-click="makeRequest(playlist.year)">{{playlist.year}}</a>
    </li>
</ul>
```

This block shows the tabs depicting each playlist. In our case these are years. `ng-repeat` does all the hard work, printing the data that is defined in our JS file. The `ng-class` is there to change the class of the tab to "active" when the tab is clicked/selected. The `ng-click` initiates a request through `makeRequest`, a function defined in our javascript file (see above).

```html
<tbody>
    <tr ng-cloak ng-repeat="video in listData.channel.item">
        <td ng-click="playVideo(video)">{{video.title}}</td>
        <td>
            <span ng-show="video.description">
                <a href="{{video.description}}" 
                   title="Download Audio">
                    <i class="icon-download-alt"></i>
                </a>
            </span>
        </td>
    </tr>
</tbody>
```

Finally the data is displayed on screen. `ng-cloak` makes sure that the content is displayed only when the data is there (otherwise browsers might show something like `{{video.description}}` which is not nice from a UI perspective). `ng-repeat` loops through the data and "prints" the table.

The description of the video is used as a storage for the URL that will point to the MP3 audio file so as the users can download it. Therefore I use `ng-show` to show the link, if it exists.

### Conclusion
This whole project was no more than 30 minutes, which included the time I had to research and experiment a bit with the [LiveStream](http://www.livestream.com/) API. This is a flexible design, with much much cleaner code (and a lot less of it). When the admin needs to add a new playlist (aka year), all they have to do is open the JS file and type a new element in the `$scope.playlists` array. The application will take care of the rest automatically.

I cannot think of doing this with less lines of code than this.

If you haven't heard of [AngularJS](http://angularjs.org/) or used it, I would highly encourage you to give it a shot. Great project, awesome support and a very very responsive, helpful and polite community.

* [AngularJS](http://angularjs.org/)
* [AngularJS documentation](http://docs.angularjs.org/api)
* [AngularJS group](https://groups.google.com/forum/#!forum/angular)
* [AngularJS Github](https://github.com/angular)
