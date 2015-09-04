    <div>
        <span class="pull-right post-date">
            <a href="post/{{ post.getSlug() }}"><i class="fa fa-file-text-o"></i></a>
            {{ post.getDate() }}
        </span>
        {{ post.getContent() }}
        <div class="tags-container">
            {% for tag in post.getTags() %}
            <a href="/tag/{{ tag }}">
                <span class="badge">{{ tag }}</span>
            </a>
            {% endfor %}
        </div>
        <div class="g-comments"
             data-href="{{ post.getGooglePlusUrl() }}"
             data-first_party_property="BLOGGER"
             data-view_type="FILTERED_POSTMOD">
        </div>
    </div>

