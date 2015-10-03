                        <li class="media list-group-item p-a">
                            <div class="media-body">
                                <div class="media-heading">
                                    <small class="pull-right text-muted">
                                        <a href="/post/{{ post['slug'] }}">
                                            <span class="icon icon-link"></span>
                                        </a>
                                        {{ post['date'] }}
                                    </small>
                                    <h3>{{ post['title'] }}</h3>
                                </div>
                                <p>
                                    {{ post['content'] }}
                                </p>
                            </div>
                            <div class="media-body-actions">
                            {% for tag in post['tags'] %}
                                <button class="btn btn-primary-outline btn-sm">
                                    {{ tag }}
                                </button>
                            {% endfor %}
                            </div>

                            {% if config.blog.disqus.enabled and showDisqus %}
                            <div class="media-body">
                                <div id="disqus_thread"></div>
                                <script type="text/javascript">
                                    var disqus_shortname  = '{{ config.blog.disqus.shortName }}';
                                    var disqus_identifier = "{{ post['disqusId'] }}";
                                    var disqus_url        = '{{ post['disqusUrl'] }}';

                                    (function () {
                                        var dsq   = document.createElement('script');
                                        dsq.type  = 'text/javascript';
                                        dsq.async = true;
                                        dsq.src   = '//' + disqus_shortname + '.disqus.com/embed.js';
                                        (document.getElementsByTagName('head')[0] ||
                                         document.getElementsByTagName('body')[0])
                                                .appendChild(dsq);
                                    })();
                                </script>
                                <noscript>
                                    Please enable JavaScript to view the
                                    <a href="https://disqus.com/?ref_noscript">
                                        comments powered by Disqus.
                                    </a>
                                </noscript>
                            </div>
                            {% endif %}
                        </li>


