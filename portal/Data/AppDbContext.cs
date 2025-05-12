using System.IO;
using Microsoft.EntityFrameworkCore;
using WebHackingPortal.Models;

namespace WebHackingPortal.Data
{
    public class AppDbContext : DbContext
    {
        public DbSet<AppUser> Users { get; set; }
        public DbSet<Lab> Labs { get; set; }
        public DbSet<Challenge> Challenges { get; set; }
        public DbSet<Submission> Submissions { get; set; }

        protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            var dbPath = Path.Combine(Directory.GetCurrentDirectory(), "portal.db");
            optionsBuilder.UseSqlite($"Data Source={dbPath}");
        }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            // Define relationships
            modelBuilder.Entity<Challenge>()
                .HasOne(c => c.Lab)
                .WithMany(l => l.Challenges)
                .HasForeignKey(c => c.LabId);

            modelBuilder.Entity<Submission>()
                .HasOne(s => s.User)
                .WithMany()
                .HasForeignKey(s => s.UserId);

            modelBuilder.Entity<Submission>()
                .HasOne(s => s.Challenge)
                .WithMany(c => c.Submissions)
                .HasForeignKey(s => s.ChallengeId);
        }
    }
}