using WebHackingPortal.Models;
using WebHackingPortal.Data;
using System.Threading.Tasks;
using Microsoft.EntityFrameworkCore;

namespace WebHackingPortal.Services
{
    public class ProgressService
    {
        private readonly AppDbContext _context;

        public ProgressService(AppDbContext context)
        {
            _context = context;
        }

        public async Task<bool> CanAccessChallenge(int userId, int challengeId)
        {
            var challenge = await _context.Challenges.FindAsync(challengeId);
            if (challenge == null || challenge.Order == 0) return false;

            if (challenge.Order == 1) return true;

            var prevChallenge = await _context.Challenges.FirstOrDefaultAsync(c => c.Order == challenge.Order - 1);
            if (prevChallenge == null) return false;

            return await _context.Submissions.AnyAsync(s =>
                s.UserId == userId &&
                s.ChallengeId == prevChallenge.Id &&
                s.IsCorrect);
        }
    }
}