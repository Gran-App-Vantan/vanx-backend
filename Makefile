.PHONY: up upd build down bash


up:
	@docker compose up
upd:
	@docker compose up -d

build:
	@docker compose build

down:
	@docker compose down

bash:
	@docker compose exec -it app bash

bash-app:
	@docker compose exec -it app bash

bash-db:
	@docker compose exec -it db bash

start:
	@docker compose build
	@docker compose up -d
	@docker compose run app bash

