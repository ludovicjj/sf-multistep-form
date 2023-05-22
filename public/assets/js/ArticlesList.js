class ArticlesList
{
    constructor(element) {
        this.element = element;
        this.sortable = Sortable.create(this.element, {
            handle: '.drag-handle',
            animation: 150,
            dataIdAttr: 'data-id',
            onEnd: async (evt) => {
                const url = evt.item.dataset.url
                await fetch(url, {
                    method: 'POST',
                    body: JSON.stringify(this.sortable.toArray())
                })
            }
        })
    }
}
new ArticlesList(document.querySelector('#js-articles-drag'))