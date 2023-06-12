---
layout: page
title: Tags
permalink: /tag
---

{% assign tags = site.tags | sort %}

<div class="widget-list rounded mb-4" data-id="widget">
    <!-- BEGIN widget-list-item -->
	{% for tag in tags %}
	{% assign posts = tag[1] %}
    <div class="widget-list-item">
        <div class="widget-list-content">
            <h4 class="widget-list-title">
                {{ tag[0] | slugify }} <a href="/tag/{{ tag[0] | slugify }}"><i class="fa fa-tag"></i></a>
            </h4>
            {% for post in posts %}
            <p class="widget-list-desc">
                <a href="{{ post.url }}">{{ post.title }} - [{{ post.date | date: "%Y-%m-%d" }}]</a>
            </p>
            {% endfor %}
        </div>
    </div>
    {% endfor %}
    <!-- END widget-list-item -->
</div>
