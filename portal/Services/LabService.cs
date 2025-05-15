using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text.Json;
using WebHackingPortal.Models;

namespace WebHackingPortal.Services
{
    public class LabService
    {
        private readonly string _labDirectory;
        private readonly List<Lab> _labs = new();

        public LabService()
        {
            _labDirectory = Path.Combine(Directory.GetCurrentDirectory(), "Labs");

            LoadLabs();
        }

        private void LoadLabs()
        {
            if (!Directory.Exists(_labDirectory))
                return;

            foreach (var file in Directory.GetFiles(_labDirectory, "*.json"))
            {
                var json = File.ReadAllText(file);
                var lab = JsonSerializer.Deserialize<Lab>(json);

                if (lab != null && !_labs.Any(l => l.Id == lab.Id))
                {
                    _labs.Add(lab);
                }
            }
        }

        public IEnumerable<Lab> GetAllLabs()
        {
            return _labs.AsReadOnly();
        }

        public Lab? GetLabById(string id)
        {
            if (int.TryParse(id, out int intId))
            {
                return _labs.FirstOrDefault(l => l.Id == intId);
            }
            return null;
        }
    }
}