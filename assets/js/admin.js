(function ($) {
    var bibleSearch;

    $(document).ready(init);

    function init() {
        bibleSearch = new BibleSearch();
    }

    function BibleSearch() {
        var $el = $('div.gw-dailythought-passage');
        if (!$el) {
            throw 'cannot find the required search module';
        }

        this.$el = $el;
        this.setup();
    }

    BibleSearch.prototype = {
        setup: function () {
            // fields
            this.$field_refid = this.$el.find('input#refid');
            this.$field_reference = this.$el.find('input#reference');
            this.$field_verse = this.$el.find('textarea#verse');

            // search
            this.$search_div = this.$el.find('.search-field');
            this.$search_spinner = this.$search_div.find('span.icon-spin');
            this.$search_query = this.$search_div.find('input#scripture-search');
            this.$search_submit = this.$search_div.find('button#scripture-submit');

            this.listen();
        },

        listen: function () {
            var $this = this;

            this.$search_submit.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $this.search($this.$search_query.val());
            });
        },

        search: function (query) {
            var $this = this;
            console.log('SEARCHING:', query);
            this.$search_submit.attr('disabled', 'disabled');
            this.$search_spinner.removeClass('hidden');
            setTimeout(function () {
                $this.$search_submit.removeAttr('disabled');
                $this.$search_spinner.addClass('hidden');
            }, 5000);
        }
    };

})(jQuery);
