            <div class="col-lg-10">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {{ post.getTitle() }}
                            <div class="pull-right">
                                <a href="post/{{ post.getSlug() }}"><i class="fa fa-file-text-o"></i></a>
                                {{ post.getDate() }}
                            </div>
                        </div>
                        <div class="panel-body">
                            {{ post.getContent() }}
                        </div>
                        <div class="panel-footer">
                            {% for tag in post.getTags() %}
                            <a href="/tag/{{ tag }}">
                                <span class="label label-primary">{{ tag }}</span>
                            </a>
                            {% endfor %}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Ads
                        </div>
                        <div class="panel-body">
                            Ads go here
                        </div>
                    </div>
                </div>

                {% if showDisqus %}
                <div class="row">
                    <div id="disqus_thread"></div>
                    <script type="text/javascript">
                        var disqus_shortname  = '{{ condig.blog.disqus.shortname }}';
                        var disqus_identifier = "{{ post.getDisqusId() }}";
                        var disqus_url        = '{{ post.getDisqusUrl() }}';

                        (function () {
                            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                        })();
                    </script>
                    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
                </div>
                {% endif %}
            </div>
