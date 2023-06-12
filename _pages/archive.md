---
layout: page
title: Archive
permalink: /archive
---

{% assign postsByYearMonth = site.posts | group_by_exp:"post", "post.date | date: '%Y %m'"  %}

<div class="widget-list rounded mb-4" data-id="widget">
    <!-- BEGIN widget-list-item -->
	{% for yearMonth in postsByYearMonth %}
		{% assign parts = yearMonth.name | split: " " %}
		{% assign year = parts[0] %}
		{% assign month = parts[1] %}
    <div class="widget-list-item">
        <div class="widget-list-content">
            <h4 class="widget-list-title">
                <a href="/{{ year }}/{{ month }}/">
                    {{ year }}-{{ month }}
                </a>
            </h4>
            {% for post in yearMonth.items %}
            <p class="widget-list-desc">
                <a href="{{ post.url }}">{{ post.title }} - [{{ post.date | date: "%Y-%m-%d" }}]</a>
            </p>
            {% endfor %}
        </div>
    </div>
    {% endfor %}
    <!-- END widget-list-item -->
</div>
