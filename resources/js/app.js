/* ---------------------------------------------------------------------------
   Public-site motion layer.
   - Scroll reveals: `.reveal` / `.reveal-media` fade-up / scale-in on enter,
     staggered through an optional inline `--reveal-delay`.
   - Parallax: `[data-parallax]` layers drift inside `.parallax-frame` at a
     fraction of scroll speed for a subtle depth cue.
   Everything is skipped entirely when the visitor prefers reduced motion.
--------------------------------------------------------------------------- */

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Simple first-look PLTS estimator. It is intentionally transparent and does
// not replace a site survey or a formal technical proposal.
window.pltsCalculator = () => ({
    monthlyBill: 1500000,
    system: 'on-grid',
    tariff: 1500,
    productionPerKwp: 105,
    targetOffset: 0.65,
    costPerKwp: {
        'on-grid': 14000000,
        hybrid: 24000000,
        'off-grid': 30000000,
    },

    get monthlyConsumption() {
        return Math.max(0, Number(this.monthlyBill) || 0) / this.tariff;
    },

    get capacity() {
        const raw = (this.monthlyConsumption * this.targetOffset) / this.productionPerKwp;

        return raw > 0 ? Math.min(100, Math.max(1.1, raw)) : 0;
    },

    get panelCount() {
        return this.capacity > 0 ? Math.ceil(this.capacity / 0.55) : 0;
    },

    get roofArea() {
        return this.capacity * 6.5;
    },

    get monthlySavings() {
        const savings = this.monthlyBill * this.targetOffset;

        return Math.max(0, Math.min(this.monthlyBill, savings));
    },

    get investment() {
        return this.capacity * this.costPerKwp[this.system];
    },

    get paybackYears() {
        const annualSavings = this.monthlySavings * 12;

        return annualSavings > 0 ? this.investment / annualSavings : 0;
    },

    currency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(Math.round(value || 0));
    },

    number(value, fractionDigits = 1) {
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: fractionDigits,
        }).format(value || 0);
    },
});

function initReveals() {
    const targets = document.querySelectorAll('.reveal, .reveal-media');

    if (targets.length === 0) {
        return;
    }

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -6% 0px' },
    );

    targets.forEach((el) => observer.observe(el));
}

function initParallax() {
    const layers = document.querySelectorAll('.parallax-frame [data-parallax]');

    if (layers.length === 0 || prefersReducedMotion) {
        return;
    }

    const strength = 44; // px of total drift across a viewport-height of travel
    let ticking = false;

    const update = () => {
        ticking = false;
        const viewport = window.innerHeight;

        layers.forEach((layer) => {
            const frame = layer.closest('.parallax-frame');

            if (!frame) {
                return;
            }

            const rect = frame.getBoundingClientRect();

            // Skip frames that are fully off-screen to keep the loop cheap.
            if (rect.bottom < -80 || rect.top > viewport + 80) {
                return;
            }

            // -0.5 (frame entering from below) .. +0.5 (frame leaving above)
            const progress = (rect.top + rect.height / 2 - viewport / 2) / viewport;
            const offset = (progress * strength).toFixed(2);

            layer.style.transform = `translate3d(0, ${offset}px, 0)`;
        });
    };

    const requestUpdate = () => {
        if (!ticking) {
            ticking = true;
            window.requestAnimationFrame(update);
        }
    };

    update();
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate);
}

initReveals();
initParallax();

/* Number counters: [data-count] ticks from 0 to its value once on enter. */
function initCounters() {
    const targets = document.querySelectorAll('[data-count]');

    if (targets.length === 0 || prefersReducedMotion || !("IntersectionObserver" in window)) {
        return;
    }

    const run = (el) => {
        const end = Number(el.dataset.count);
        const duration = 1600;
        const start = performance.now();

        const tick = (now) => {
            const t = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - t, 3);

            el.textContent = Math.round(end * eased).toLocaleString('id-ID');

            if (t < 1) {
                requestAnimationFrame(tick);
            }
        };

        requestAnimationFrame(tick);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    run(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.6 },
    );

    targets.forEach((el) => {
        el.textContent = 0;
        observer.observe(el);
    });
}

initCounters();

/* Wordmark drift: [data-drift] eases upward a little as the page scrolls. */
function initDrift() {
    const el = document.querySelector('[data-drift]');

    if (!el || prefersReducedMotion) {
        return;
    }

    let ticking = false;

    const update = () => {
        ticking = false;
        const y = Math.min(window.scrollY, window.innerHeight);

        el.style.transform = `translate3d(0, ${(-y * 0.22).toFixed(1)}px, 0)`;
    };

    window.addEventListener(
        'scroll',
        () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        },
        { passive: true },
    );
}

initDrift();

/* Solar showcase: draggable marquee with momentum and a seamless loop. */
function initDraggableMarquees() {
    const marquees = document.querySelectorAll('[data-draggable-marquee]');

    marquees.forEach((root) => {
        const track = root.querySelector('[data-marquee-track]');
        const originalItems = Array.from(track?.children ?? []);

        if (!track || originalItems.length === 0) {
            return;
        }

        // Keep two extra copies so the loop remains filled while the user drags.
        for (let copy = 1; copy < 3; copy += 1) {
            originalItems.forEach((item) => {
                const clone = item.cloneNode(true);
                clone.dataset.marqueeCopy = 'duplicate';
                clone.setAttribute('aria-hidden', 'true');
                clone.querySelectorAll('a, button, [tabindex]').forEach((focusable) => {
                    focusable.setAttribute('tabindex', '-1');
                });
                track.appendChild(clone);
            });
        }

        let singleSetWidth = 0;
        let x = 0;
        let throwVelocity = 0;
        let rafId = null;
        let isVisible = !('IntersectionObserver' in window);
        let isDragging = false;
        let pointerId = null;
        let lastPointerX = 0;
        let lastPointerTime = 0;
        let dragDistance = 0;
        let suppressClick = false;

        const clamp = (value, min, max) => Math.min(Math.max(value, min), max);
        const setX = () => {
            track.style.transform = `translate3d(${x.toFixed(2)}px, 0, 0)`;
        };
        const wrap = (value) => {
            if (!singleSetWidth) {
                return value;
            }

            while (value <= -singleSetWidth) value += singleSetWidth;
            while (value > 0) value -= singleSetWidth;

            return value;
        };
        const measure = () => {
            const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || 0);
            const widths = originalItems.reduce((sum, item) => sum + item.getBoundingClientRect().width, 0);

            singleSetWidth = widths + gap * Math.max(0, originalItems.length - 1);
            x = wrap(x);
            setX();
        };
        const stopAnimation = () => {
            if (rafId !== null) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        };
        const tick = () => {
            rafId = requestAnimationFrame(tick);

            if (!isVisible || isDragging || prefersReducedMotion) {
                return;
            }

            // A measured, steady pace keeps the motion visible without feeling rushed.
            x -= 0.48;
            x += throwVelocity;
            throwVelocity *= 0.96;

            if (Math.abs(throwVelocity) < 0.01) {
                throwVelocity = 0;
            }

            x = wrap(x);
            setX();
        };
        const startAnimation = () => {
            if (rafId === null && !prefersReducedMotion) {
                rafId = requestAnimationFrame(tick);
            }
        };
        const finishDrag = (event) => {
            if (!isDragging || (event && event.pointerId !== pointerId)) {
                return;
            }

            isDragging = false;
            root.classList.remove('is-dragging');
            if (root.hasPointerCapture?.(pointerId)) {
                root.releasePointerCapture(pointerId);
            }
            pointerId = null;
            suppressClick = dragDistance > 8;
            startAnimation();
        };

        measure();
        window.addEventListener('resize', measure, { passive: true });

        root.addEventListener('pointerdown', (event) => {
            if (event.button !== undefined && event.button !== 0) {
                return;
            }

            isDragging = true;
            pointerId = event.pointerId;
            lastPointerX = event.clientX;
            lastPointerTime = performance.now();
            dragDistance = 0;
            throwVelocity = 0;
            root.classList.add('is-dragging');
            root.setPointerCapture?.(pointerId);
        });

        root.addEventListener('pointermove', (event) => {
            if (!isDragging || event.pointerId !== pointerId) {
                return;
            }

            const now = performance.now();
            const deltaX = event.clientX - lastPointerX;
            const deltaTime = now - lastPointerTime;

            x = wrap(x + deltaX);
            setX();
            dragDistance += Math.abs(deltaX);

            if (deltaTime > 0) {
                throwVelocity = clamp((deltaX / deltaTime) * 16.67 * 1.8, -32, 32);
            }

            lastPointerX = event.clientX;
            lastPointerTime = now;
        });

        root.addEventListener('pointerup', finishDrag);
        root.addEventListener('pointercancel', finishDrag);
        root.addEventListener('click', (event) => {
            if (!suppressClick) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            suppressClick = false;
        }, true);
        root.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
                return;
            }

            event.preventDefault();
            throwVelocity = 0;
            x = wrap(x + (event.key === 'ArrowLeft' ? 1 : -1) * root.clientWidth * 0.35);
            setX();
        });

        if ('ResizeObserver' in window) {
            const resizeObserver = new ResizeObserver(measure);
            resizeObserver.observe(track);
            originalItems.forEach((item) => resizeObserver.observe(item));
        }

        if (prefersReducedMotion) {
            root.classList.add('is-visible');
            stopAnimation();
            return;
        }

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        isVisible = entry.isIntersecting;
                        if (isVisible) startAnimation();
                    });
                },
                { threshold: 0.15 },
            );
            observer.observe(root);
        }

        startAnimation();
    });
}

initDraggableMarquees();

/* FAQ thread: one controlled height animation per item, plus arrow navigation. */
function initFaqThreads() {
    document.querySelectorAll('[data-faq-thread]').forEach((list) => {
        const items = Array.from(list.querySelectorAll('.faq-thread-item'));
        const triggers = items.map((item) => item.querySelector('[data-faq-trigger]')).filter(Boolean);

        const setOpen = (item, shouldOpen) => {
            const trigger = item.querySelector('[data-faq-trigger]');
            const panel = item.querySelector('[data-faq-panel]');

            if (!trigger || !panel) {
                return;
            }

            const isOpen = item.classList.contains('is-open');

            if (isOpen === shouldOpen) {
                return;
            }

            if (prefersReducedMotion) {
                item.classList.toggle('is-open', shouldOpen);
                trigger.setAttribute('aria-expanded', String(shouldOpen));
                panel.hidden = !shouldOpen;
                panel.style.height = shouldOpen ? 'auto' : '';
                return;
            }

            if (shouldOpen) {
                panel.hidden = false;
                panel.style.height = '0px';
                void panel.offsetHeight;

                item.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                panel.style.height = `${panel.scrollHeight}px`;

                const onOpenEnd = (event) => {
                    if (event.target !== panel || event.propertyName !== 'height' || !item.classList.contains('is-open')) {
                        return;
                    }

                    panel.style.height = 'auto';
                    panel.removeEventListener('transitionend', onOpenEnd);
                };

                panel.addEventListener('transitionend', onOpenEnd);
                return;
            }

            panel.style.height = `${panel.getBoundingClientRect().height}px`;
            void panel.offsetHeight;

            item.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
            panel.style.height = '0px';

            const onCloseEnd = (event) => {
                if (event.target !== panel || event.propertyName !== 'height' || item.classList.contains('is-open')) {
                    return;
                }

                panel.hidden = true;
                panel.style.height = '';
                panel.removeEventListener('transitionend', onCloseEnd);
            };

            panel.addEventListener('transitionend', onCloseEnd);
        };

        items.forEach((item) => {
            const trigger = item.querySelector('[data-faq-trigger]');

            trigger?.addEventListener('click', () => {
                setOpen(item, !item.classList.contains('is-open'));
            });
        });

        triggers.forEach((trigger, index) => {
            trigger.addEventListener('keydown', (event) => {
                if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') {
                    return;
                }

                event.preventDefault();
                const direction = event.key === 'ArrowDown' ? 1 : -1;
                const nextIndex = (index + direction + triggers.length) % triggers.length;
                triggers[nextIndex].focus();
            });
        });
    });
}

initFaqThreads();

/* Homepage rail navigation: a lightweight SVG path that follows the reader. */
function initHomeRail() {
    const rail = document.querySelector('[data-home-rail]');

    if (!rail || !window.matchMedia('(min-width: 1280px)').matches) {
        return;
    }

    const links = Array.from(rail.querySelectorAll('[data-rail-link]'));
    const list = rail.querySelector('.home-rail-toc-list');
    const svg = rail.querySelector('[data-rail-svg]');
    const dashedPath = rail.querySelector('[data-rail-path]');
    const filledPath = rail.querySelector('[data-rail-fill]');
    const plane = rail.querySelector('[data-rail-plane]');
    const revealTarget = document.getElementById('home-showcase');
    const targets = links
        .map((link) => document.getElementById(link.dataset.railTarget))
        .filter(Boolean);

    if (!list || !svg || !dashedPath || !filledPath || !plane || links.length === 0 || targets.length !== links.length) {
        return;
    }

    let nodes = [];
    let lengths = [];
    let totalLength = 0;
    let activeIndex = 0;
    let lastScrollY = window.scrollY;
    let ticking = false;

    const buildPath = (points) => {
        if (!points.length) {
            return '';
        }

        const commands = [`M ${points[0].x} 0`, `L ${points[0].x} ${points[0].y}`];

        points.slice(1).forEach((point, index) => {
            const previous = points[index];

            if (previous.x !== point.x) {
                const bend = (point.y - previous.y) * 0.3;
                commands.push(`L ${previous.x} ${previous.y + bend}`);
                commands.push(`L ${point.x} ${point.y - bend}`);
            }

            commands.push(`L ${point.x} ${point.y}`);
        });

        return commands.join(' ');
    };

    const lengthAtY = (y) => {
        let low = 0;
        let high = totalLength;

        for (let step = 0; step < 20; step += 1) {
            const middle = (low + high) / 2;

            if (dashedPath.getPointAtLength(middle).y < y) {
                low = middle;
            } else {
                high = middle;
            }
        }

        return high;
    };

    const renderProgress = (progress, direction = 'down') => {
        if (!totalLength) {
            return;
        }

        const distance = Math.min(Math.max(progress, 0), totalLength);
        const point = dashedPath.getPointAtLength(distance);
        const behind = dashedPath.getPointAtLength(Math.max(0, distance - 1));
        const ahead = dashedPath.getPointAtLength(Math.min(totalLength, distance + 1));
        const angle = (Math.atan2(ahead.y - behind.y, ahead.x - behind.x) * 180) / Math.PI + 90 + (direction === 'up' ? 180 : 0);
        const reached = lengths.filter((length) => length <= distance + 0.5).length;
        const nextActive = Math.max(0, reached - 1);

        rail.style.setProperty('--rail-plane-x', `${point.x}px`);
        rail.style.setProperty('--rail-plane-y', `${point.y}px`);
        rail.style.setProperty('--rail-plane-turn', `${angle}deg`);
        filledPath.style.strokeDasharray = `${distance} ${totalLength}`;

        links.forEach((link, index) => {
            link.classList.toggle('is-reached', index < reached);
            link.classList.toggle('is-active', index === nextActive);
        });

        activeIndex = nextActive;
    };

    const measure = () => {
        nodes = links.map((link) => ({
            x: 5 + Number(link.style.getPropertyValue('--rail-depth') || 0) * 14,
            y: link.offsetTop + link.offsetHeight / 2,
        }));

        const path = buildPath(nodes);
        const width = Math.max(...nodes.map((node) => node.x)) + 12;
        const height = list.offsetHeight;

        svg.setAttribute('width', `${width}`);
        svg.setAttribute('height', `${height}`);
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
        dashedPath.setAttribute('d', path);
        filledPath.setAttribute('d', path);

        totalLength = dashedPath.getTotalLength();
        lengths = nodes.map((node) => lengthAtY(node.y));
        update();
    };

    const getProgress = () => {
        const marker = Math.max(112, window.innerHeight * 0.34);
        const tops = targets.map((target) => target.getBoundingClientRect().top);
        const index = tops.reduce((current, top, itemIndex) => (top <= marker ? itemIndex : current), -1);

        if (index < 0) {
            return lengths[0] ?? 0;
        }

        if (index >= lengths.length - 1) {
            return lengths[lengths.length - 1] ?? totalLength;
        }

        const span = tops[index + 1] - tops[index];
        const sectionProgress = span > 0 ? Math.min(Math.max((marker - tops[index]) / span, 0), 1) : 0;

        return lengths[index] + (lengths[index + 1] - lengths[index]) * sectionProgress;
    };

    const update = () => {
        ticking = false;
        const direction = window.scrollY >= lastScrollY ? 'down' : 'up';

        renderProgress(getProgress(), direction);
        rail.classList.toggle('is-visible', !revealTarget || revealTarget.getBoundingClientRect().top <= 112);
        lastScrollY = window.scrollY;
    };

    const requestUpdate = () => {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(update);
        }
    };

    links.forEach((link, index) => {
        link.addEventListener('click', (event) => {
            const target = targets[index];

            if (!target) {
                return;
            }

            event.preventDefault();
            renderProgress(lengths[index], index >= activeIndex ? 'down' : 'up');
            target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
            window.history.replaceState(null, '', `#${target.id}`);
        });
    });

    const resizeObserver = new ResizeObserver(measure);
    resizeObserver.observe(list);
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', measure);
    measure();
}

initHomeRail();
