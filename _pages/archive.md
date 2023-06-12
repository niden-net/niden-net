---
layout: page
title: Archive
permalink: /archive
---


{% assign postsByYearMonth = site.posts | group_by_exp:"post", "post.date | date: '%Y %m'"  %}
{% for yearMonth in postsByYearMonth %}
	{% assign parts = yearMonth.name | split: " " %}
	{% assign year = parts[0] %}
	{% assign month = parts[1] %}
<h5 id="{{ year | append:month }}">{{ year }}-{{ month }}</h5> 
	{% assign posts = yearMonth.items %}
	{% for post in posts %}
[{{ post.title }} - [{{ post.date | date: "%Y-%m-%d" }}]]({{ post.url }})
	{% endfor %}
{% endfor %}
