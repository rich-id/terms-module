# Configuration

Here is a sample of configuration:

```yaml
rich_id_terms_module:
    admin_roles: [ ROLE_GIGA_ADMIN ]
    default_refusal_route: app_home
    access_denied_redirection: [ 'app_front_protected_by_terms' ]
```

- `rich_id_terms_module.admin_roles`: Defines the roles that are allowed to generate and edit unpublished versions.
- `rich_id_terms_module.default_refusal_route`: If no route is given to the signing page, then the user is redirected to this page
- `rich_id_terms_module.access_denied_redirection`: Routes that supports the redirection to the signing page if the voter denies the access
