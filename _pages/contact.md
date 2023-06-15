---
layout: page
title: Contact
permalink: /contact
---

<p class="m-b-30">
    If you have a project you would like to discuss, get in touch with me. I 
    would be happy to help you find the best solution for your needs.
</p>
<!-- begin row -->
<div class="row row-space-30">
    <!-- begin col-12 -->
    <div class="col-md">
        <form class="form-horizontal" 
              name="niden-net-contact" 
              method="post"
              data-netlify="true" 
              data-netlify-recaptcha="true">
            <div style="display: none;">
                <label>
                    Don't fill this out if you’re human:
                    <input name="bot-field" />
                </label>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-md-3 text-md-right">
                    Name <span class="text-danger">*</span>
                </label>
                <div class="col-md-9">
                    <input type="text" name="query-name" class="form-control">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-md-3 text-md-right">
                    Email <span class="text-danger">*</span>
                </label>
                <div class="col-md-9">
                    <input type="text" name="query-email" class="form-control">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-md-3 text-md-right">
                    Message <span class="text-danger">*</span>
                </label>
                <div class="col-md-9">
                    <textarea name="query-message" class="form-control" rows="10"></textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-md-3 text-md-right">
                </label>
                <div class="col-md-9">
                    <div data-netlify-recaptcha="true"></div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-md-3 text-md-right"></label>
                <div class="col-md-9 text-left">
                    <button type="submit" class="btn btn-dark btn-lg btn-block">
                        Send Message
                    </button>
                </div>
            </div>
        </form>
    </div>
    <!-- end col-8 -->
</div>
<!-- end row -->


<div class="modal fade" 
     id="modal-contact" 
     tabindex="-1" 
     role="dialog" 
     aria-labelledby="modal-contact-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" 
                class="close" 
                data-dismiss="modal" 
                aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modal-contact-label"></h4>
      </div>
      <div class="modal-body" id="modal-contact-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
            Close
        </button>
      </div>
    </div>
  </div>
</div>

<script type="application/javascript">
    document
        .querySelector("form")
        .addEventListener("submit", handleSubmit);

    const handleSubmit = (event) => {
        event.preventDefault();

        const contactForm = event.target;
        const contactModal = $('#modal-contact');
        const formData = new FormData(contactForm);
        var payload = {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(formData).toString(),
        };
        
        contactModal.modal({ show: false });
        
        fetch("/", payload)
            .then(() => {
                $('#modal-contact-label').innerHtml('Confirmation');
                $('#modal-contact-body').innerHtml('Thank you for your query. We will get back to you shortly.');
                contactModal.show();
                window.location.reload();
            })
            .catch((error) => {
                $('#modal-contact-label').innerHtml('Error');
                $('#modal-contact-body').innerHtml(error);
                contactModal.show();

            });
    };
</script>
