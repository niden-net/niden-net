---
layout: page
title: Tags
permalink: /tags
---

{% assign tags = site.tags | sort %}
{% for tag in tags %}
<a name="{{ tag | first | slugify }}"></a>
##### {{ tag[0] }} 
	{% assign posts = tag[1] %}
	{% for post in posts %}
[{{ post.title }} - [{{ post.date | date: "%Y-%m-%d" }}]]({{ post.url }})
	{% endfor %}
{% endfor %}

