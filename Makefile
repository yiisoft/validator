.DEFAULT_GOAL := help

help: ## Show available commands
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
	| awk 'BEGIN {FS = ":.*?## "}; {printf "  %-20s %s\n", $$1, $$2}'

po4a: ## Run translation tools
	./docs/prepare-config.sh && \
	docker run --rm \
		--user $(shell id -u):$(shell id -g) \
		-v $(PWD):/src \
		-w /src/docs \
		--init \
		ghcr.io/yiisoft-contrib/po4a:0.74 \
		po4a.conf
