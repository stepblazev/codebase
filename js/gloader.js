window.Gloader = {
    // элементы DOM, с которыми работает лоадер
    nodes: {
        root: document.querySelector('.gloader'),               // основной элемент лоадера
        caption: document.querySelector('.gloader__caption'),   // элемент для отображения текста загрузки
    },
    
    // конфиг по умолчанию
    config: {
        activeClass: '_active',    // класс, который активирует лоадер
        baseCaption: 'загрузка',   // текст загрузки по умолчанию
        showCaption: true,         // показывать ли текст загрузки
        lockScroll: true,          // блокировать ли прокрутку при активном лоадере
    },
    
    // включает лоадер с подписью
    enable: function (caption) {
        if (!caption) caption = this.config.baseCaption;

        this.nodes.caption.textContent = this.config.showCaption ? caption : '';
        this.nodes.root.classList.add(this.config.activeClass);

        if (this.config.lockScroll) {
            document.body.style.overflow = 'hidden';
        }
    },
    
    // выключает лоадер
    disable: function () {
        this.nodes.root.classList.remove(this.config.activeClass);

        if (this.config.lockScroll) {
            document.body.style.overflow = '';
        }
    },
    
    // переключает состояние лоадера (вкл/выкл)
    toggle: function (state = !this.isEnabled(), caption) {
        state ? this.enable(caption) : this.disable();
    },
    
    // проверяет, активен ли сейчас лоадер
    isEnabled: function () {
        return this.nodes.root.classList.contains(this.config.activeClass);
    },
    
}