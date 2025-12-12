{
  description = "Multi arch nix flake for PHP 7.4 development";

  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs?ref=nixos-unstable";
    flake-utils.url = "github:numtide/flake-utils";
    nix-phps.url = "github:fossar/nix-phps";
  };

  outputs =
    {
      self,
      nixpkgs,
      flake-utils,
      nix-phps,
      ...
    }@inputs:

    flake-utils.lib.eachDefaultSystem (
      system:
      let
        pkgs = import nixpkgs {
          inherit system;
          config.allowUnfree = true;
        };

        php = nix-phps.packages.${system}.php74;

        mkScript = name: text: pkgs.writeShellScriptBin name text;

        scripts = [
          (mkScript "php-debug-adapter" ''
            node ${pkgs.vscode-extensions.xdebug.php-debug}/share/vscode/extensions/xdebug.php-debug/out/phpDebug.js
          '')
        ];

        phpWithExtensions = php.buildEnv {
          extensions = (
            { enabled, all }:
            enabled
            ++ (with all; [
              xdebug
              intl
              mysqli
              bcmath
              curl
              zip
              soap
              mbstring
              gd
            ])
          );
          extraConfig = ''
            xdebug.mode=debug
            xdebug.start_with_request=yes
            xdebug.client_host=127.0.0.1
            xdebug.client_port=9003
            xdebug.log_level = 0
          '';
        };

        devPackages = with pkgs; [
          curl
          nodejs_22
          phpWithExtensions
          phpWithExtensions.packages.composer
          pnpm
          unzip
          vscode-extensions.xdebug.php-debug
          zip
        ];

        postShellHook = '''';
      in
      {
        devShells.default = pkgs.mkShell {
          name = "php74-dev-shell";
          nativeBuildInputs = scripts;
          packages = devPackages;
          postShellHook = postShellHook;
        };
      }
    );
}
