---
layout: page
title: Tags
permalink: /tags
---

{% assign tags = site.tags | sort %}
{% for tag in tags %}
<h5 id="{{ tag[0] | slugify }}">{{ tag[0] }}</h5> 
	{% assign posts = tag[1] %}
	{% for post in posts %}
[{{ post.title }} - [{{ post.date | date: "%Y-%m-%d" }}]]({{ post.url }})
	{% endfor %}
{% endfor %}
