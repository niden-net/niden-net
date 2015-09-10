    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title">{{ post.getTitle() }}</h1>
                    <!--
                    <span class="pull-right">
                        <a href="post/{{ post.getSlug() }}"><i class="fa fa-file-text-o"></i></a>
                        {{ post.getDate() }}
                    </span>
                    -->
                </div>
                <div class="panel-body">
                    {{ post.getContent() }}
                </div>
                <div class="panel-footer">
                    <span class="pull-right">
                        {% for tag in post.getTags() %}
                        <a href="/tag/{{ tag }}">
                            <span class="label label-info">{{ tag }}</span>
                        </a>
                        {% endfor %}
                    </span>
                    <div class="clearfix"></div>
                </div>
            </div>
            {% if posts|length < 2 %}
            <div class="g-comments"
                 data-href="{{ post.getGooglePlusUrl() }}"
                 data-first_party_property="BLOGGER"
                 data-view_type="FILTERED_POSTMOD">
            </div>
            {% endif %}
        </div>
    </div>
    <!-- /.row -->
