<img class="post-image" src="{{ cdnUrl }}/files/2009-12-01-angular.png" />

Continued from [Part I](/post/world-with-angular-part-i)

### The Wildlife Rescue League application

#### Design

For those that do not know, the [Wildlife Rescue League](http://www.wildliferescueleague.org/)

> is a non-profit organization providing care for sick, injured and orphaned wildlife in order to return them to the wild. Our licensed rehabilitators, located throughout Virginia and suburban Maryland, work with animal shelters, humane societies, wildlife groups, nature centers and veterinary hospitals to provide care to creatures in need. WRL operates a wildlife hotline in the Northern Virginia and surrounding areas to assist the public in obtaining information and assistance in locating a wildlife rehabilitator. WRL is also committed to educating the public about the natural history of native wildlife, coexisting with it and preventing the need for wildlife rehabilitation. We can provide brochures, educational material and educational programs to suit your needs.

The WRL hotline records all phone calls that the volunteers answer and organizes that data in a manner that would help the organization in the future (anticipated call volume etc.) In the past the data collection method was  a simple sheet of paper that was mailed to one volunteer, who then had to decode everything and create the relevant spreadsheet for data analysis. Later on this model evolved into a spreadsheet which was copied and distributed to the volunteers. Again the data had to be collected (via email now) and merged for a meaningful analysis to take place.

I have therefore created a simple application using `<angular/>` to allow the volunteers to enter their data in a centralized repository. I am going to explain in detail how I utilized `<angular/>` to perform this task, needing only a few hours (mostly spent in cosmetic changes) until I had a working copy of what I wanted.

This blog post will be missing the authentication mechanism and a couple of other bits and pieces. I am hoping to show you a different way or programming and encourage you to explore `<angular/>` and its power.

Thinking about my application I will need to store the data. I will need the following fields:

```html
Date
Shift Start
Shift End
Animal Code
Situation Code
Resolution Code
City
State
Description of incident
```

#### Creating the database

The data needs to be stored in the database. So logging into my account in getangular.com (do so if you haven't done this already) I created my library and my new database (both called testwrl) and I am set. Everything else will be controlled in the HTML document.

##### Initial HTML file

My HTML file is very simple.

```html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<meta name="robots" content="index, follow" />
<style type="text/css">
body {
    font-family:Verdana,Arial,Helvetica;
    font-size:10pt;
}
th,td {
    text-alignment:center;
}
th, .inputBox {
    background:#000066;
    color:#00FF00;
    font-weight:bold;
}
.incident {
    border:1px solid #000000;
}
.incidentheader {
    padding:5px;
    border:1px solid #000000;
    font-weight:bold;
    background:#000099;
    color:#00EE00;
}
.centercell {
    text-align:center;
}
img {
    border:none;
}
</style>
<script type="text/javascript"
        src="http://testwrl.getangular.com/angular-1.0a.js#database=testwrl"></script>
</head>
<body>
</body>
</html>
```

Note the JavaScript line at the bottom part of the snippet. It references a subdomain of getangular.com (`testwrl.getangular.com`) as well as the database I am using to store data (`database=testwrl`).

##### Creating the HTML input elements

I need to create a form to store the data. The programming is done with HTML `<input>` elements. Once I have everything mapped nicely on screen I need to bind them in the database. First of all I need to describe what I want to work with. I will use the `ng-entity` attribute in my body element. The `ng-entity` uses the expression `[instance=]Entity[:template]`. Effectively `Entity` is the name of the entity that will be stored in my database under that name.

Therefore in my HTML file I need to change the body element:

```html
<body ng-entity="incident=Incident" ng-init="incidents=Incident.all()">
```

So I am storing the `Incident` in the database. `incident` is the document of this entity (`Incident`) that is stored under the name of that instance.

The `ng-init` declaration in the body element assigns all the records of the entity `Incident` (`Incident.all()`) to a variable (`incidents`). The name of the variable is arbitrary - you can choose whatever you like.

Since `incident` is the document, I need to reference all my elements (the input ones) towards that. The way this is done is via the name attribute of each of the HTML elements. So for instance for the Date that I want to store, my HTML `input` declaration changes to:

```html
<input name="incident.shiftdate" />
```

I repeat the same methodology and I name my elements `incident.shiftstart`, `incident.shiftend`, `incident.animalcode` etc.

Notable is the fact that I haven't used the `<form>` element at all. I am going to add a Save button at the end of this HTML block which is nothing more than a `submit` element:

```html
<input type="submit" value="Save" />
```

`<angular/>` takes care of all the data posting from the browser to the server so I do not need any `<form>` elements and POST control.

The HTML script as is right now is below:

```html
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
               "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type"
         content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="description"
         content="Wildlife Rescue League Hotline" />
<meta name="keywords"
         content="WRL, wildlife, hotline, volunteer work" />
<meta name="robots" content="index, follow" />
<style type="text/css">
body {
  font-family:Verdana,Arial,Helvetica;
  font-size:10pt;
}
th,td {
  text-alignment:center;
}
th, .inputBox {
  background:#000066;
  color:#00FF00;
  font-weight:bold;
}
.incident {
  border:1px solid #000000;
}
.incidentheader {
  padding:5px;
  border:1px solid #000000;
  font-weight:bold;
  background:#000099;
  color:#00EE00;
}
.centercell {
  text-align:center;
}
img {
  border:none;
}
</style>
<script type="text/javascript"
  src="http://testwrl.getangular.com/angular-1.0a.js#database=testwrl"></script>
</head>
<body ng-entity="incident=Incident" ng-init="incidents=Incident.all()">
  <span style='float:right;font-size:10px;'>
    Powered by <a href='http://www.getangular.com'>`<angular/>`</a>
  </span>
  <br />
  <div class="incident">
    <div class="incidentheader">WRL Hotline Incidents</div>
    [ <a href="#">New Incident</a> ]
    <br />
    <table style='width:100%'>
    <tr>
      <td style='text-align:right;'>
        <label>Date</label>
      </td>
      <td>
        <input name="incident.shiftdate" />
      </td>
      <td style='text-align:right;'>
        <label>Shift Start/End</label>
      </td>
      <td>
        <select name="incident.shiftstart">
          <option value=""></option>
          <option value="08:00 AM">08:00 AM</option>
          <option value="08:30 AM">08:30 AM</option>
          <option value="09:00 AM">09:00 AM</option>
          <option value="09:30 AM">09:30 AM</option>
          <option value="10:00 AM">10:00 AM</option>
          <option value="10:30 AM">10:30 AM</option>
          <option value="11:00 AM">11:00 AM</option>
          <option value="11:30 AM">11:30 AM</option>
          <option value="12:00 PM">12:00 PM</option>
          <option value="12:30 PM">12:30 PM</option>
          <option value="13:00 PM">01:00 PM</option>
          <option value="13:30 PM">01:30 PM</option>
          <option value="14:00 PM">02:00 PM</option>
          <option value="14:30 PM">02:30 PM</option>
          <option value="15:00 PM">03:00 PM</option>
          <option value="15:30 PM">03:30 PM</option>
          <option value="16:00 PM">04:00 PM</option>
          <option value="16:30 PM">04:30 PM</option>
          <option value="17:00 PM">05:00 PM</option>
          <option value="17:30 PM">05:30 PM</option>
          <option value="18:00 PM">06:00 PM</option>
          <option value="18:30 PM">06:30 PM</option>
          <option value="19:00 PM">07:00 PM</option>
          <option value="19:30 PM">07:30 PM</option>
          <option value="20:00 PM">08:00 PM</option>
          <option value="20:30 PM">08:30 PM</option>
        </select>
         /
        <select name="incident.shiftend">
          <option value=""></option>
          <option value="08:00 AM">08:00 AM</option>
          <option value="08:30 AM">08:30 AM</option>
          <option value="09:00 AM">09:00 AM</option>
          <option value="09:30 AM">09:30 AM</option>
          <option value="10:00 AM">10:00 AM</option>
          <option value="10:30 AM">10:30 AM</option>
          <option value="11:00 AM">11:00 AM</option>
          <option value="11:30 AM">11:30 AM</option>
          <option value="12:00 PM">12:00 PM</option>
          <option value="12:30 PM">12:30 PM</option>
          <option value="13:00 PM">01:00 PM</option>
          <option value="13:30 PM">01:30 PM</option>
          <option value="14:00 PM">02:00 PM</option>
          <option value="14:30 PM">02:30 PM</option>
          <option value="15:00 PM">03:00 PM</option>
          <option value="15:30 PM">03:30 PM</option>
          <option value="16:00 PM">04:00 PM</option>
          <option value="16:30 PM">04:30 PM</option>
          <option value="17:00 PM">05:00 PM</option>
          <option value="17:30 PM">05:30 PM</option>
          <option value="18:00 PM">06:00 PM</option>
          <option value="18:30 PM">06:30 PM</option>
          <option value="19:00 PM">07:00 PM</option>
          <option value="19:30 PM">07:30 PM</option>
          <option value="20:00 PM">08:00 PM</option>
          <option value="20:30 PM">08:30 PM</option>
        </select>
      </td>
    </tr>

    <tr>
      <td style='width:20%;text-align:right;'>
        <label>Animal Code</label>
      </td>
      <td>
        <select name="incident.animalcode">
          <option value=""></option>
          <option value="B">Songbird</option>
          <option value="C">Corvine</option>
          <option value="M">Mammal</option>
          <option value="R">Raptor</option>
          <option value="RE">Reptile</option>
          <option value="U">Unknown</option>
          <option value="W">Waterfowl</option>

          <option value="OTH">Other</option>
        </select>
      </td>
      <td style='text-align:right;'>
        <label>Situation Code</label>
      </td>
      <td>
        <select name="incident.situationcode">
          <option value=""></option>
          <option value="A">Attacked (details for attacker)</option>
          <option value="I">Injured</option>
          <option value="K">Killed</option>
          <option value="N">Nuisance (explain in details)</option>
          <option value="O">Orphaned</option>
          <option value="OTH">Other (explain in details)</option>
          <option value="U">Unknown</option>
        </select>

        <label>Resolution Code</label>

        <select name="incident.resolutioncode">
          <option value=""></option>
          <option value="D">Died</option>
          <option value="GA">Gave advice only</option>
          <option value="LM">Left Message</option>
          <option value="OTH">Other</option>
          <option value="RR">Referred to Rehabber</option>
          <option value="RS">Referred to Shelter</option>
          <option value="RV">Referred to Vet</option>
          <option value="WCB">Watch and Call Back</option>
        </select>
      </td>
    </tr>
    <tr>

      <td style='width:20%;text-align:right;'>
        <label>City</label>
      </td>
      <td>
        <input name="incident.city" type="text" />
      </td>
      <td style='text-align:right;'>
        <label>State</label>
      </td>
      <td>
        <select name="incident.state">
          <option value=""></option>
          <option value="MD">Maryland</option>
          <option value="VA">Virginia</option>
          <option value="DC">Washington DC</option>
        </select>
      </td>
    </tr>
    <tr>
      <td style='width:20%;text-align:right;'>
        <label>Description</label>
      </td>
      <td colspan="3">
        <textarea name="incident.details" rows="5" cols="80"></textarea>
      </td>
    </tr>
    </table>
    <input type="submit" value="Save" class="inputBox" />
  </div>
```

##### Adding data

The entry screen is complete and if you type some data and click the Save button, you will store that data in the database. You will notice that this is the case since your URL will change to something like

```html
.../#incident=abcdefg7896456464532132135498a
```

That is effectively the id of that record. If you delete that #incident..... and reload the page, you will be presented with an empty form where you can add more data. Clicking the Save button will save that record and you will notice that the new id is different than the previous one.

I really don't want to have to delete the #incident.... from my address bar every time I want to add a new record. I am sure that the users of the WRL will not like it either. For that, I have added a link at the top of the screen which is already in the script that I have a few lines up. The line:

```html
[ <a href="#">New Incident</a> ]
```

is the one that will reload the page allowing me to add a new record whenever I click that link.

Continued in [Part III](/post/world-with-angular-part-iii)
