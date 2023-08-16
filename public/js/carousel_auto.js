class Carousel {
	/**
	 * @param {HTMLElement} element
	 * @param {Object} options
	 * @param {Object} options.slidesToScroll Nombre d'éléments à faire défiler
	 * @param {Object} options.slidesVisible Nombre d'éléments visibles dans un slide
	 */
	constructor(element, options = {}) {
		this.element = element;
		this.options = Object.assign(
			{},
			{
				slidesToScroll: 3,
				slidesVisible: 1,
			},
			options
		);
		this.visible = this.options.slidesVisible;
		this.slides = this.options.slidesToScroll;
		let children = [].slice.call(element.children);
		let root = this.createDivWithClass("carousel");
		this.container = this.createDivWithClass("carousel__container");
		root.appendChild(this.container);
		this.element.appendChild(root);
		this.cards = children.map((child) => {
			let card = this.createDivWithClass("carousel__cards");
			card.appendChild(child);
			this.container.appendChild(card);
			return card;
		});
		this.setStyle();

		this.kTimer = 0
		setInterval(() => this.updateCardsLocation(this), 5000);
	}
	setStyle() {
		let ratio = this.slides / this.visible;
		this.container.style.width = ratio * 100 + "%";
		this.cards.forEach((card) => (card.style.width = 100 / this.visible / ratio + "%"));
	}

	updateCardsLocation(context) {
		context.kTimer++;
		let ratio = this.slides / this.visible;
		context.container.style.transform = `translateX(-${100 / ratio * (context.kTimer % this.cards.length)}%)`;
	}

	/**
	 * @param {string} className
	 * @returns {HTMLElement}
	 */
	createDivWithClass(className) {
		let div = document.createElement("div");
		div.setAttribute("class", className);
		return div;
	}
}

document.addEventListener("DOMContentLoaded", function () {
	new Carousel(document.getElementById("carousel1")),
		{
			slidesToScroll: 3,
			slidesVisible: 1,
		};
});

