---
layout: post
title: Localization and Country Regions
date: 2012-03-05T23:45:00.000Z
tags:
  - localization
  - github
  - json
---
I was recently assigned a task in my current job, to try and standardize address related data.
<img class="post-image" src="/files/2012-03-05-world.png" />

My approach was to use the ISO codes for countries and ISO codes for regions. A region is defined as the geographical split of areas in a country. For instance in the US regions are called states, in Canada provinces etc.

I have used several sources on the internet but my main source was [Wikipedia](http://en.wikipedia.org/wiki/ISO_3166-2).

In order to complete this task I used [Zend Locale](http://framework.zend.com/manual/en/zend.locale.html) to retrieve the available countries and using the ISO codes (keys of the returned data).

My method is:

```php
/**
 * Gets the countries based on a passed (or default) locale
 * 
 * @param string  $locale
 * @param boolean  $sort
 */
public static function getCountries($locale = NULL, $sort = TRUE)
{
    $results = parent::getTranslationList('territory', $locale, 2);
    
    if ($sort) 
    {
        asort($results);
    }

    return $results;
}
```

Having the list of countries and their ISO codes, allowed me to have a proper storage of country related data in my addresses table. The use of a select box in the view layer ensures that I keep this standardization in place.

Extending that, I wanted to have an easy way to access the region related data, without having to hit the database. I therefore used JSON encoded files that contain an array with the type of the region as the key and Region ISO Code => Region Name as the array elements. Again I used a select box for that in the view layer, which is automatically refreshed when a new country is selected.

So for instance for the US the JSON file is as follows:

```json
{
    "state":{
        "US-AL":"Alabama",
        "US-AK":"Alaska",
        ...
        "US-WV":"West Virginia",
        "US-WI":"Wisconsin",
        "US-WY":"Wyoming"
    }
}
```

where the key defines the region - in this case a State, and the array elements are the relevant data. In some instances I wanted to give a bit more selection and visual aids to the end user. I therefore have several sub arrays denoted by *optiongroup* tags for the relevant select box showing the regions.

In the case of Great Britain:

```json
{
    "county":{
        "England":{
            "GB-BKM":"Buckinghamshire",
            "GB-CAM":"Cambridgeshire",
            ...
            "GB-WOK":"Wokingham",
            "GB-YOR":"York"
        },
        "Northern Ireland":{
            "GB-ANT":"Antrim",
            "GB-ARD":"Ards",
            ...
            "GB-OMH":"Omagh",
            "GB-STB":"Strabane"
        },
        "Scotland":{
            "GB-ABE":"Aberdeen City",
            "GB-ABD":"Aberdeenshire",
            ...
            "GB-WDU":"West Dunbartonshire",
            "GB-WLN":"West Lothian"
        },
        "Wales":{
            "GB-BGW":"Blaenau Gwent",
            "GB-BGE":"Bridgend (Pen-y-bont ar Ogwr)",
            ...
            "GB-VGL":"Vale of Glamorgan (Bro Morgannwg)",
            "GB-WRX":"Wrexham (Wrecsam)"
        }
    }
}
```

So with simple commands as `file_get_contents` and `json_decode` (or using `Zend_Json::decode`) I was able to have a flexible system that would load standardized address related data (regions and countries).

You can download the list of JSON files (one per country) from my [Github Repo](https://github.com/niden/Localized_World_Regions).

The list I have there is by no means complete, but it does have a lot of data that one can use. Additions are always welcome :)

Enjoy!
