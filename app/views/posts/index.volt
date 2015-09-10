<div>
    <span class="pull-left">
        {% if pages['previous'] > 0 %}
            <a href="/{{ pages['previous'] }}"><i class="fa fa-fast-backward"></i></a>
        {% endif %}
    </span>
    <span class="pull-right">
        {% if pages['next'] > 0 %}
            <a href="/{{ pages['next'] }}"><i class="fa fa-fast-forward"></i></a>
        {% endif %}
    </span>
</div>
{% for post in posts %}
    {% include 'posts/view.volt' %}
    <div class="text-center horizontal-ruler">
        011011100110100101100100011001010110111000101110011011100110010101110100
    </div>
{% endfor %}
<div>
    <span class="pull-left">
        {% if pages['previous'] > 0 %}
            <a href="/{{ pages['previous'] }}"><i class="fa fa-fast-backward"></i></a>
        {% endif %}
    </span>
    <span class="pull-right">
        {% if pages['next'] > 0 %}
            <a href="/{{ pages['next'] }}"><i class="fa fa-fast-forward"></i></a>
        {% endif %}
    </span>
</div>
