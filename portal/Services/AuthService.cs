using Microsoft.AspNetCore.Authentication.Cookies;
using Microsoft.AspNetCore.Authentication;
using Microsoft.AspNetCore.Http;
using System.Security.Claims;
using WebHackingPortal.Models;
using System.Threading.Tasks;
using System.Collections.Generic;

namespace WebHackingPortal.Services
{
    public class AuthService
    {
        private readonly IHttpContextAccessor _httpContextAccessor;

        public AuthService(IHttpContextAccessor httpContextAccessor)
        {
            _httpContextAccessor = httpContextAccessor;
        }

        public async Task LoginAsync(AppUser user)
        {
            var claims = new List<Claim>
            {
                new Claim(ClaimTypes.NameIdentifier, user.Id.ToString()),
                new Claim(ClaimTypes.Name, user.Username)
            };

            var identity = new ClaimsIdentity(claims, CookieAuthenticationDefaults.AuthenticationScheme);
            var principal = new ClaimsPrincipal(identity);

            var httpContext = _httpContextAccessor.HttpContext;
            if (httpContext != null)
            {
                await httpContext.SignInAsync(CookieAuthenticationDefaults.AuthenticationScheme, principal);
            }
            else
            {
                throw new System.InvalidOperationException("HttpContext is null.");
            }
        }

        public async Task LogoutAsync()
        {
            var httpContext = _httpContextAccessor.HttpContext;
            if (httpContext != null)
            {
                await httpContext.SignOutAsync(CookieAuthenticationDefaults.AuthenticationScheme);
            }
            else
            {
                throw new System.InvalidOperationException("HttpContext is null.");
            }
        }

        public int? GetUserId()
        {
            var userIdString = _httpContextAccessor.HttpContext?.User.FindFirstValue(ClaimTypes.NameIdentifier);
            return userIdString != null ? int.Parse(userIdString) : (int?)null;
        }
    }
}