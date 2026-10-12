import Alpine from 'alpinejs';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

window.Alpine = Alpine;

const afterLoad = (callback) => {
    if (document.readyState === 'complete') {
        callback();
    } else {
        window.addEventListener('load', callback, { once: true });
    }
};

Alpine.data('heroSlideshow', (total) => ({
    current: 0,
    shown: 0,
    playing: !reduceMotion && total > 1,
    hovered: false,
    focused: false,
    loaded: [0],
    ready: [0],
    timer: null,
    init() {
        if (this.playing) {
            this.restart();
            afterLoad(() => this.load(1));
        }
    },
    destroy() {
        clearInterval(this.timer);
    },
    restart() {
        clearInterval(this.timer);
        this.timer = setInterval(() => {
            if (this.playing && !this.hovered && !this.focused && !document.hidden) {
                this.show((this.current + 1) % total);
            }
        }, 6000);
    },
    load(index) {
        if (!this.loaded.includes(index)) {
            this.loaded.push(index);
        }
    },
    photoReady(index) {
        if (!this.ready.includes(index)) {
            this.ready.push(index);
        }

        if (this.current === index) {
            this.shown = index;
        }
    },
    show(index) {
        this.load(index);
        this.current = index;

        if (this.ready.includes(index)) {
            this.shown = index;
        }

        if (this.playing) {
            this.load((index + 1) % total);
        }
    },
    pick(index) {
        this.playing = false;
        this.show(index);
    },
    toggle() {
        this.playing = !this.playing;

        if (this.playing) {
            this.focused = false;
            this.load((this.current + 1) % total);
            this.restart();
        }
    },
}));
Alpine.start();

const canvases = document.querySelectorAll('canvas[data-chart]');

const whenIdle = (callback) => ('requestIdleCallback' in window ? window.requestIdleCallback(callback, { timeout: 3000 }) : setTimeout(callback, 1000));

const siteFont = () => Promise.race([
    document.fonts.load(`1em ${getComputedStyle(document.body).fontFamily}`).catch(() => {}),
    new Promise((resolve) => setTimeout(resolve, 2500)),
]);

if (canvases.length > 0) {
    const fontReady = siteFont();

    import('chart.js').then(async ({ Chart, BarController, BarElement, DoughnutController, ArcElement, CategoryScale, LinearScale, Legend, Tooltip }) => {
        await fontReady;
        Chart.register(BarController, BarElement, DoughnutController, ArcElement, CategoryScale, LinearScale, Legend, Tooltip);
        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        Chart.defaults.color = '#45556c';

        if (reduceMotion) {
            Chart.defaults.animation = false;
        }

        const draw = (canvas, animate = true) => {
            if (Chart.getChart(canvas)) {
                return;
            }

            const config = JSON.parse(canvas.dataset.chart);

            new Chart(canvas, animate ? config : { ...config, options: { ...config.options, animation: false } });
        };

        if (!('IntersectionObserver' in window)) {
            canvases.forEach((canvas) => draw(canvas));

            return;
        }

        const chartObserver = new IntersectionObserver((entries, observer) => {
            entries.filter((entry) => entry.isIntersecting).forEach((entry) => {
                draw(entry.target);
                observer.unobserve(entry.target);
            });
        }, { threshold: 0, rootMargin: '0px 0px 100px 0px' });

        canvases.forEach((canvas) => chartObserver.observe(canvas));

        afterLoad(() => whenIdle(() => canvases.forEach((canvas) => {
            draw(canvas, false);
            chartObserver.unobserve(canvas);
        })));
    });
}

if (!reduceMotion && 'IntersectionObserver' in window) {
    document.documentElement.classList.add('js-motion');

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.filter((entry) => entry.isIntersecting).forEach((entry) => {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0, rootMargin: '0px 0px -10% 0px' });

    document.querySelectorAll('[data-reveal]').forEach((element) => revealObserver.observe(element));
}
