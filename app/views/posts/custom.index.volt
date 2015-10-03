            {% for post in posts %}
                {% include 'posts/custom.view.volt' %}
            {% endfor %}
            {% include 'posts/custom.paginator.volt' %}
