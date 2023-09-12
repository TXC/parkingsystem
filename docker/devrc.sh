# !/bin/sh

alias phpunit="php ./vendor/bin/phpunit --no-coverage $@"
alias p="php ./vendor/bin/phpunit --no-coverage"
alias pf="php ./vendor/bin/phpunit --no-coverage --filter $1"

alias slimbox="php ./vendor/bin/slimbox "$@""
alias slim:up="slimbox --no-interaction migrations:execute --up -- "$@""
alias slim:down="slimbox --no-interaction migrations:execute --down -- "$@""
alias slim:diff="slimbox --no-interaction migrations:diff"
alias slim:migrate="slimbox --no-interaction migrations:migrate"
