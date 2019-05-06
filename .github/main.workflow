workflow "NPM Install && Test" {
  resolves = ["Test"]
  on = "pull_request"
}

action "Install" {
  uses = "actions/npm@master"
  args = "install"
}

action "Test" {
  needs = "Install"
  uses = "actions/npm@master"
  args = "test"
}
