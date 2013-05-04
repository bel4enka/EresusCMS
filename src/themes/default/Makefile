BUILD_DIR= ./build

build:
	@find blocks -name '*.css' -printf '@import url("%p");\n' > theme.import.css
	@borschik --input theme.import.css --output theme.css
	@rm theme.import.css
	@mkdir -p $(BUILD_DIR)
	@mv theme.css $(BUILD_DIR)/theme.css
	@mv i $(BUILD_DIR)/i
	@cp theme.js $(BUILD_DIR)/theme.js
	@cp theme.yml $(BUILD_DIR)/theme.yml
	@cp -r images $(BUILD_DIR)/i

clean:
	@if [ -d $(BUILD_DIR) ]; then rm -r $(BUILD_DIR); fi

.PHONY: build clean
