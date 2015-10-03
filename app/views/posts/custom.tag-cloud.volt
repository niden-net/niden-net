
                    <div class="panel panel-default panel-link-list">
                        <div class="panel-body">
                            <h5 class="m-t-0">Tag Cloud</h5>
                            {% for tag, class in tagCloud %}

                            <span style="font-size: {{ class }}">
                                <a href='/tag/{{ tag }}'>{{ tag }}</a>
                            </span>{% endfor %}

                        </div>
                    </div>
