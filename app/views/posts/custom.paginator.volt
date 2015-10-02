                        {% if pages['previous'] > 0 or pages['next'] > 0 %}
                        <li class="media list-group-item p-a">
                            <div class="media-body">
                                {% if pages['previous'] %}
                                <span class="pull-left">
                                    <button class="btn btn-primary-outline">
                                        <a href="/{{ pages['previous'] }}">
                                            <span class="icon icon-controller-fast-backward"></span>
                                        </a>
                                    </button>
                                </span>
                                {% endif %}
                                {% if pages['next'] %}
                                <span class="pull-right">
                                    <button class="btn btn-primary-outline">
                                        <a href="/{{ pages['next'] }}">
                                            <span class="icon icon-controller-fast-forward"></span>
                                        </a>
                                    </button>
                                </span>
                            {% endif %}
                            </div>
                        </li>
                        {% endif %}
