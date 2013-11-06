TARGET_DIR ?= build

build: prepare optimize
	@mv theme.css $(TARGET_DIR)/
	@cp -r theme.* images img misc SiteSettings auth.css $(TARGET_DIR)/
	@if [ -d i ]; then mv i $(TARGET_DIR)/; fi

prepare: clean
	@mkdir -p $(TARGET_DIR)

theme.css:
	@echo -n > imports.css
	@echo '@import url(auth.css);' >> imports.css
	@echo '@import url(main.css);' >> imports.css
	@echo '@import url(css.src/typography.css);' >> imports.css
	@echo '@import url(css.src/layout.css);' >> imports.css
	@echo '@import url(css.src/widgets.css);' >> imports.css
	@find blocks -name '*.css' -print0 | xargs -0 printf "@import url(%s);\n" >> imports.css
	@borschik -t css --input=imports.css --output=theme.css --freeze=yes
	@rm imports.css

optimize: theme.css
	@if [ -d i ]; then find i -path '*.png' | xargs optipng -o7; fi

clean:
	@if [ -d $(TARGET_DIR) ]; then rm -rf $(TARGET_DIR); fi

.PHONY: build prepare optimize clean
