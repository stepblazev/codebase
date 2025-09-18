window.Gloader = {
    element: document.querySelector('.gloader'),
    captionElement: document.querySelector('.gloader__caption'),
    
    enable: function (caption = "Загрузка") {
        this.captionElement.textContent = caption;
        this.element.classList.add('_active');
        document.body.style.overflow = 'hidden';
    },
    
    disable: function () {
        this.element.classList.remove('_active');
        document.body.style.overflow = '';
    },
}